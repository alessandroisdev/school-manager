<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cronograma Escolar</title>
    <style>
        @page { size: landscape; margin: 10mm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #{{ $settings->primary_color ?? '000' }}; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { max-height: 60px; margin-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #{{ $settings->primary_color ?? '000' }}; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .title { background-color: #{{ $settings->primary_color ?? '000' }}; color: #fff; padding: 8px; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; }
        .no-print { text-align: right; margin-bottom: 20px; }
        .btn-print { padding: 10px 20px; background-color: #000; color: #fff; text-decoration: none; border-radius: 4px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Imprimir Documento</button>
    </div>

    <!-- CRONOGRAMA POR TURMA (GERAL) -->
    @if(!$teacherId)
        <div class="header">
            @if(isset($settings->unit_logo))
                <img src="{{ Storage::url($settings->unit_logo) }}" alt="Logo">
            @endif
            <h1>{{ $settings->receipt_header ?? 'SGE School Manager' }}</h1>
            <p>Cronograma Geral de Aulas - {{ date('Y') }}</p>
        </div>

        @foreach($groupedByClass as $classId => $schedules)
            @php $class = $schedules->first()->schoolClass; @endphp
            <div class="title">Turma: {{ $class->name }} ({{ $class->shift->name }})</div>
            <table>
                <thead>
                    <tr>
                        <th>Horário</th>
                        <th>Segunda</th>
                        <th>Terça</th>
                        <th>Quarta</th>
                        <th>Quinta</th>
                        <th>Sexta</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $timeSlots = App\Domains\Academic\Models\TimeSlot::where('shift_id', $class->shift_id)->orderBy('start_time')->get();
                    @endphp
                    @foreach($timeSlots as $slot)
                        <tr>
                            <td><strong>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</strong></td>
                            @for($day = 1; $day <= 5; $day++)
                                @php
                                    $sched = $schedules->where('day_of_week', $day)->where('time_slot_id', $slot->id)->first();
                                @endphp
                                <td>
                                    @if($sched)
                                        <strong>{{ $sched->teacherAssignment->subject->name ?? 'N/A' }}</strong><br>
                                        <span style="font-size: 9px; color: #555;">{{ $sched->teacher->employee->name }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="page-break-after: always;"></div>
        @endforeach
    @endif

    <!-- CRONOGRAMA POR PROFESSOR (INDIVIDUAL) -->
    @if($teacherId || $groupedByTeacher->count() > 0)
        @foreach($groupedByTeacher as $tId => $schedules)
            @php $teacher = $schedules->first()->teacher; @endphp
            <div class="header">
                @if(isset($settings->unit_logo))
                    <img src="{{ Storage::url($settings->unit_logo) }}" alt="Logo">
                @endif
                <h1>{{ $settings->receipt_header ?? 'SGE School Manager' }}</h1>
                <p>Cronograma Individual - Docente</p>
            </div>

            <div class="title">Professor(a): {{ $teacher->employee->name }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Horário</th>
                        <th>Segunda</th>
                        <th>Terça</th>
                        <th>Quarta</th>
                        <th>Quinta</th>
                        <th>Sexta</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Obter todos os slots para englobar turnos possiveis
                        $timeSlots = App\Domains\Academic\Models\TimeSlot::where('unit_id', session('active_unit_id'))->orderBy('start_time')->get();
                    @endphp
                    @foreach($timeSlots as $slot)
                        @php
                            // Check if teacher has any class in this slot across the week to avoid printing empty rows
                            $hasSlot = $schedules->where('time_slot_id', $slot->id)->count() > 0;
                        @endphp
                        @if($hasSlot)
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</strong></td>
                                @for($day = 1; $day <= 5; $day++)
                                    @php
                                        $sched = $schedules->where('day_of_week', $day)->where('time_slot_id', $slot->id)->first();
                                    @endphp
                                    <td>
                                        @if($sched)
                                            <strong>{{ $sched->teacherAssignment->subject->name ?? 'N/A' }}</strong><br>
                                            <span style="font-size: 9px; color: #555;">{{ $sched->schoolClass->name }} ({{ $sched->schoolClass->grade->name }})</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div style="page-break-after: always;"></div>
        @endforeach
    @endif

</body>
</html>
