<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Контроль выполнения процессов</title>
    <style>
        body { font-family: system-ui, Arial, sans-serif; margin: 2rem; color: #222; }
        h1 { font-size: 1.5rem; margin-bottom: 1rem; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d0d0d0; padding: .5rem .75rem; text-align: left; font-size: .9rem; }
        th { background: #f5f5f5; }
        tr.failed td { background: #fdecea; color: #a3160b; }
        td.empty { text-align: center; color: #777; }
    </style>
</head>
<body>
    <h1>Контроль выполнения процессов</h1>

    <table>
        <thead>
            <tr>
                <th>Дата процесса</th>
                <th>Время запуска</th>
                <th>Время выполнения, с</th>
                <th>Идентификатор процесса</th>
                <th>Статус процесса</th>
                <th>Файл</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($processes as $process)
            <tr @class(['failed' => $process->hasFailed()])>
                <td>{{ $process->rp_start_datetime->format('d.m.Y') }}</td>
                <td>{{ $process->rp_start_datetime->format('H:i:s') }}</td>
                <td>{{ $process->rp_exec_time ?? '—' }}</td>
                <td>{{ $process->rp_pid }}</td>
                <td>{{ $process->status?->ps_name }}</td>
                <td>
                    @if ($process->hasReportFile())
                        <a href="{{ route('processes.download', $process) }}">
                            {{ basename($process->rp_file_save_path) }}
                        </a>
                    @else
                        —
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td class="empty" colspan="6">Процессы ещё не запускались.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
