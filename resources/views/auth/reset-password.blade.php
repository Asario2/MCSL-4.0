<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Passwort zurücksetzen</title>
</head>
<body>

<h2>Neues Passwort vergeben</h2>

@if (session('status'))
    <div style="color: green;">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ url('/reset-password') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div>
        <label>Email:</label>
        <input type="email" name="email" value="{{ $email ?? old('email') }}" required>
    </div>

    <div>
        <label>Neues Passwort:</label>
        <input type="password" name="password" required>
    </div>

    <div>
        <label>Passwort bestätigen:</label>
        <input type="password" name="password_confirmation" required>
    </div>

    <button type="submit">
        Passwort zurücksetzen
    </button>
</form>

</body>
</html>
