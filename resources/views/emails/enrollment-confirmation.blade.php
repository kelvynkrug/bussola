<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmação de Matrícula</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .info-box {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }
        .course-info {
            background-color: #e8f5e8;
            border-left-color: #27ae60;
        }
        .student-info {
            background-color: #fff3cd;
            border-left-color: #f39c12;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        h1, h2 {
            margin-top: 0;
        }
        .highlight {
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Confirmação de Matrícula</h1>
        <p>Sistema de Gerenciamento Escolar</p>
    </div>

    <div class="content">
        <p>Olá <span class="highlight">{{ $student->name }}</span>,</p>

        <p>Sua matrícula foi confirmada com sucesso! Abaixo estão os detalhes da sua matrícula:</p>

        <div class="info-box student-info">
            <h2>Dados do Aluno</h2>
            <p><strong>Nome:</strong> {{ $student->name }}</p>
            <p><strong>CPF:</strong> {{ $student->cpf }}</p>
            <p><strong>E-mail:</strong> {{ $student->email }}</p>
            <p><strong>Data de Nascimento:</strong> {{ $student->birth_date->format('d/m/Y') }}</p>
        </div>

        <div class="info-box course-info">
            <h2>Dados do Curso</h2>
            <p><strong>Nome do Curso:</strong> {{ $course->name }}</p>
            <p><strong>Descrição:</strong> {{ $course->description }}</p>
            <p><strong>Carga Horária:</strong> {{ $course->workload }} horas</p>
        </div>

        <div class="info-box">
            <h2>Informações da Matrícula</h2>
            <p><strong>Data da Matrícula:</strong> {{ $enrollment->enrolled_at->format('d/m/Y H:i') }}</p>
            <p><strong>Status:</strong> <span class="highlight">{{ ucfirst($enrollment->status) }}</span></p>
        </div>

        <p>Se você tiver alguma dúvida ou precisar de mais informações, entre em contato conosco.</p>

        <p>Atenciosamente,<br>
        <strong>Equipe do Sistema de Gerenciamento Escolar</strong></p>
    </div>

    <div class="footer">
        <p>Este é um e-mail automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} Sistema de Gerenciamento Escolar. Todos os direitos reservados.</p>
    </div>
</body>
</html>
