<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h3>Assessment Report</h3>
    <table>
        <thead>
        <tr>
            <th>Slno</th><th>Batch</th><th>Technology</th><th>Employee</th>
            <th>Phone</th><th>Mark</th><th>Grade</th><th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($report as $row)
            <tr>
                <td>{{ $row->slno }}</td>
                <td>{{ $row->Batch_Name }}</td>
                <td>{{ $row->Technology_Name }}</td>
                <td>{{ $row->Employee_Name }}</td>
                <td>{{ $row->Employee_Phone }}</td>
                <td>{{ $row->mark }}</td>
                <td>{{ $row->grade }}</td>
                <td>{{ $row->status }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
