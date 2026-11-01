#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
arima_forecast.py - ARIMA(2,1,1) untuk EWS Syafana Fried Chicken.

Dipanggil oleh EwsForecastCommand.php via shell_exec().
Output: satu baris JSON valid ke stdout, TIDAK PERNAH traceback mentah.

Output sukses:
  {
    "forecast": [{"tanggal": "YYYY-MM-DD", "nilai": float}, ...],  // 7 item
    "mape": float,
    "rmse": float,
    "mae":  float
  }

Output error:
  {"error": "pesan dalam Bahasa Indonesia"}

Argumen CLI:
  --cutoff YYYY-MM-DD        Training hanya pakai data WHERE tanggal <= cutoff.
                             Forecast 7 hari-aktif berikutnya (offset cutoff).
  --env   /path/.env         Path ke file .env Laravel (default: .env di parent).
  --wfv-initial-window INT   Ukuran window awal WFV (default: 70% data, min 15).

Arsitektur dua model (TIDAK boleh tertukar):
  Model A - Evaluasi  : WFV expanding window -> mape/rmse/mae out-of-sample.
  Model B - Produksi  : Fit sekali pada SELURUH data -> forecast 7 hari.
"""

import argparse
import json
import math
import os
import sys
from datetime import timedelta
import warnings

warnings.filterwarnings('ignore')

try:
    import mysql.connector
    from statsmodels.tsa.arima.model import ARIMA
    HAVE_LIBS = True
except ImportError as _e:
    HAVE_LIBS = False
    _IMPORT_ERROR = str(_e)


# --- Helper: keluarkan error JSON dan exit ------------------------------------

def _exit_error(pesan: str, code: int = 1) -> None:
    """Cetak JSON error ke stdout lalu exit. TIDAK PERNAH mencetak traceback."""
    sys.stdout = sys.__stdout__
    print(json.dumps({'error': pesan}), flush=True)
    sys.exit(code)


# --- Helper: pencarian & resolve path file .env -------------------------------

def resolve_env_path(cli_env_arg: str | None) -> tuple[str | None, list[str]]:
    """
    Tentukan path file .env yang akan digunakan.

    Kembalikan tuple (env_path_ditemukan, list_candidates_dicoba).
    Logic pencarian dipertahankan PERSIS seperti versi sebelumnya.
    """
    candidates = [
        os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '.env'),
        os.path.join(os.getcwd(), '.env'),
    ]

    env_path = cli_env_arg
    if not env_path:
        for c in candidates:
            if os.path.exists(c):
                env_path = c
                break

    return env_path, candidates


# --- Helper: load .env --------------------------------------------------------

def load_env(path: str) -> dict:
    """Parse file .env Laravel sederhana (tanpa shell interpolation)."""
    env = {}
    if not os.path.exists(path):
        return env
    with open(path, encoding='utf-8') as f:
        for line in f:
            line = line.strip()
            if not line or line.startswith('#') or '=' not in line:
                continue
            k, v = line.split('=', 1)
            v = v.strip().strip('"').strip("'")
            env[k.strip()] = v
    return env


# --- Helper: koneksi database MySQL ------------------------------------------

def connect_db(env: dict):
    """
    Buka koneksi mysql.connector berdasarkan environment dict.
    Raise Exception jika koneksi gagal (ditangani oleh caller).
    """
    return mysql.connector.connect(
        host=env.get('DB_HOST', '127.0.0.1'),
        port=int(env.get('DB_PORT', 3306)),
        user=env.get('DB_USERNAME', 'root'),
        password=env.get('DB_PASSWORD', ''),
        database=env.get('DB_DATABASE', 'ews_syafana'),
    )


# --- Helper: fetch data omzet dari database ----------------------------------

def fetch_data_omzet(conn, cutoff: str | None) -> list[tuple]:
    """
    Jalankan query SELECT tanggal, jumlah_omzet dari tabel omzet.
    Filter WHERE tanggal <= cutoff jika cutoff diisi.
    Raise Exception jika query gagal (ditangani oleh caller).
    """
    cur = conn.cursor()
    try:
        if cutoff:
            query = (
                'SELECT tanggal, jumlah_omzet FROM omzet '
                'WHERE tanggal <= %s ORDER BY tanggal ASC'
            )
            cur.execute(query, (cutoff,))
        else:
            query = (
                'SELECT tanggal, jumlah_omzet FROM omzet '
                'ORDER BY tanggal ASC'
            )
            cur.execute(query)
        return cur.fetchall()
    finally:
        cur.close()


# --- Helper: penentuan initial window WFV -------------------------------------

def tentukan_initial_window(n: int, wfv_initial_window_arg: int | None) -> int:
    """
    Hitung dan validasi ukuran initial_window untuk WFV.

    Raise ValueError jika argumen bernilai di luar batas validasi.
    """
    if wfv_initial_window_arg is not None:
        initial_window = wfv_initial_window_arg
        if initial_window < 15:
            raise ValueError(
                f'--wfv-initial-window terlalu kecil ({initial_window}). '
                'Minimum 15.'
            )
        if initial_window >= n:
            raise ValueError(
                f'--wfv-initial-window ({initial_window}) >= jumlah data ({n}). '
                'Tidak ada fold WFV yang bisa dieksekusi.'
            )
    else:
        initial_window = max(15, int(n * 0.70))
        if initial_window >= n:
            initial_window = n - 1

    return initial_window


# --- Helper: hitung metrik ----------------------------------------------------

def compute_metrics(actual: list, predicted: list) -> dict:
    """
    Hitung MAPE, RMSE, MAE dari pasangan (actual, predicted).
    Digunakan pada hasil WFV (genuinely out-of-sample), bukan fittedvalues.
    """
    n = len(actual)
    if n == 0:
        return {'mape': None, 'rmse': None, 'mae': None}

    mape_sum = 0.0
    sse      = 0.0
    mae_sum  = 0.0
    mape_n   = 0

    for a, p in zip(actual, predicted, strict=True):
        err      = a - p
        sse     += err ** 2
        mae_sum += abs(err)
        if a != 0:
            mape_sum += abs(err / a)
            mape_n   += 1

    mape = round((mape_sum / mape_n) * 100, 4) if mape_n > 0 else None
    rmse = round(math.sqrt(sse / n), 2)
    mae  = round(mae_sum / n, 2)

    return {'mape': mape, 'rmse': rmse, 'mae': mae}


# --- Model A: Walk-Forward Validation (expanding window) ----------------------

def walk_forward_validation(values: list, initial_window: int) -> dict:
    """
    Evaluasi genuinely out-of-sample dengan expanding window.

    Parameter
    ---------
    values         : deret hari-aktif (float), urut dari terlama ke terbaru.
    initial_window : jumlah titik untuk window training pertama (>= 15).

    Kembalikan dict berisi:
      'actual'    : list nilai aktual pada titik yang diforecast
      'predicted' : list nilai forecast 1-langkah dari masing-masing fold
      'n_folds'   : total fold yang berhasil dieksekusi
      'n_skipped' : total fold yang diskip karena fit gagal
    """
    actual_list    = []
    predicted_list = []
    n_skipped      = 0

    total = len(values)
    for i in range(initial_window, total):
        train    = values[:i]
        true_val = values[i]

        try:
            mdl = ARIMA(train, order=(2, 1, 1)).fit()
            fc  = float(mdl.forecast(steps=1)[0])
        except Exception:
            n_skipped += 1
            continue

        actual_list.append(true_val)
        predicted_list.append(fc)

    return {
        'actual':    actual_list,
        'predicted': predicted_list,
        'n_folds':   len(actual_list),
        'n_skipped': n_skipped,
    }


def walk_forward_validation_lr(values: list, initial_window: int) -> dict:
    """Evaluasi genuinely out-of-sample Regresi Linear dengan expanding window."""
    try:
        import numpy as np
    except ImportError:
        return {'mape': None, 'rmse': None, 'mae': None}

    actual_list    = []
    predicted_list = []

    total = len(values)
    for i in range(initial_window, total):
        train    = values[:i]
        true_val = values[i]

        try:
            x = np.arange(len(train))
            y = np.array(train, dtype=float)
            poly = np.polyfit(x, y, 1)
            fc  = float(poly[0] * len(train) + poly[1])
        except Exception:
            continue

        actual_list.append(true_val)
        predicted_list.append(fc)

    return compute_metrics(actual_list, predicted_list)


def fit_final_and_forecast(values: list, last_date, conn=None, cutoff: str | None = None, steps: int = 7) -> list:
    """
    Fit ARIMA(2,1,1) pada SELURUH values, forecast `steps` hari-aktif ke depan.
    Jika cutoff diisi dan data omzet setelah cutoff tersedia di DB (mode simulasi/backtest),
    gunakan tanggal hari-aktif buka sesungguhnya dari DB.
    """
    mdl = ARIMA(values, order=(2, 1, 1)).fit()
    fc  = mdl.forecast(steps=steps)

    future_dates = []
    if conn and cutoff:
        try:
            cur = conn.cursor()
            query = 'SELECT tanggal FROM omzet WHERE tanggal > %s ORDER BY tanggal ASC LIMIT %s'
            cur.execute(query, (cutoff, steps))
            rows = cur.fetchall()
            cur.close()
            future_dates = [r[0].isoformat() if hasattr(r[0], 'isoformat') else str(r[0]) for r in rows]
        except Exception:
            future_dates = []

    forecast = []
    d = last_date
    for i, val in enumerate(fc):
        if i < len(future_dates):
            tgl_str = future_dates[i]
        else:
            d = d + timedelta(days=1)
            tgl_str = d.isoformat() if hasattr(d, 'isoformat') else str(d)

        forecast.append({
            'tanggal': tgl_str,
            'nilai':   round(float(val), 2),
        })
    return forecast


# --- Main ---------------------------------------------------------------------

def main():
    """Entry point. Jaring pengaman: tidak ada exception yang bocor ke stdout."""
    try:
        _run()
    except SystemExit:
        raise
    except Exception as e:
        _exit_error(f'Kesalahan tak terduga di script Python: {str(e)}')


def _run():
    """Implementasi utama; dibungkus main() untuk tangkap exception global."""
    if not HAVE_LIBS:
        _exit_error(
            f'Library belum terinstal: {_IMPORT_ERROR}. '
            'Jalankan: pip install statsmodels mysql-connector-python'
        )

    # --- Parse argumen CLI ---
    parser = argparse.ArgumentParser(description='ARIMA forecast EWS Syafana')
    parser.add_argument(
        '--cutoff', type=str, default=None,
        help='Tanggal cutoff YYYY-MM-DD (training <= cutoff)'
    )
    parser.add_argument(
        '--env', type=str, default=None,
        help='Path ke file .env Laravel'
    )
    parser.add_argument(
        '--wfv-initial-window', type=int, default=None,
        dest='wfv_initial_window',
        help='Ukuran window awal WFV (default: 70%% total data, min 15)'
    )
    args = parser.parse_args()

    # --- Resolve path file .env ---
    env_path, candidates = resolve_env_path(args.env)
    if not env_path or not os.path.exists(env_path):
        c_str = [os.path.normpath(c) for c in candidates]
        _exit_error(
            f'File .env tidak ditemukan. Sudah dicoba: {c_str}. '
            'Gunakan argumen --env /path/ke/.env'
        )

    env = load_env(env_path)

    # --- Koneksi MySQL & Fetch Data ---
    try:
        conn = connect_db(env)
    except Exception as e:
        _exit_error(f'Koneksi MySQL gagal: {str(e)}')

    try:
        rows = fetch_data_omzet(conn, args.cutoff)
    except Exception as e:
        conn.close()
        _exit_error(f'Query database gagal: {str(e)}')

    if len(rows) < 15:
        conn.close()
        _exit_error(
            f'Data terlalu sedikit ({len(rows)} baris). '
            'Minimal 15 hari-aktif diperlukan untuk ARIMA.'
        )

    values    = [float(r[1]) for r in rows]
    dates     = [r[0] for r in rows]
    last_date = dates[-1]
    n         = len(values)

    # --- Tentukan window awal WFV ---
    try:
        initial_window = tentukan_initial_window(n, args.wfv_initial_window)
    except ValueError as e:
        conn.close()
        _exit_error(str(e))

    # --- MODEL A: Walk-Forward Validation -> mape/rmse/mae ---
    wfv_result = walk_forward_validation(values, initial_window)

    if wfv_result['n_folds'] == 0:
        conn.close()
        _exit_error(
            f'Semua fold Walk-Forward Validation gagal. '
            f'Total fold dicoba: {n - initial_window}, '
            f'diskip: {wfv_result["n_skipped"]}. '
            'Coba kurangi --wfv-initial-window atau periksa data.'
        )

    metrics    = compute_metrics(wfv_result['actual'], wfv_result['predicted'])
    metrics_lr = walk_forward_validation_lr(values, initial_window)

    # --- MODEL B: Fit final pada seluruh data -> forecast produksi ---
    try:
        forecast = fit_final_and_forecast(values, last_date, conn=conn, cutoff=args.cutoff, steps=7)
    except Exception as e:
        _exit_error(f'ARIMA fit/forecast produksi gagal: {str(e)}')
    finally:
        conn.close()

    # --- Output JSON ---
    print(json.dumps({
        'forecast': forecast,
        'mape':     metrics['mape'],
        'rmse':     metrics['rmse'],
        'mae':      metrics['mae'],
        'comparison': {
            'arima':          metrics,
            'regresi_linear': metrics_lr,
        }
    }), flush=True)


if __name__ == '__main__':
    main()
