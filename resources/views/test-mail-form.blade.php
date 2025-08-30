<!DOCTYPE html>
<html>
<head>
    <title>Send Test Mail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>Send Test Stripe Notification Email</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('test.mail.send') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Enter Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Send Test Email</button>
    </form>
</body>
</html>
