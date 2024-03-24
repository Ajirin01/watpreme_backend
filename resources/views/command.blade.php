<!-- resources/views/run-command.blade.php -->

<form action="{{ route('run.command') }}" method="post">
    @csrf
    <label for="password">Password:</label>
    <input type="password" name="password" required style="width:100%"><br><br>
    <label for="command">Command:</label>
    <input type="text" name="command" required style="width:100%"><br><br>
    <button type="submit">Run Command</button>
</form>

@if(session('error'))
    <div>{{ session('error') }}</div>
@endif

@if(session('success'))
    <div>{{ session('success') }}</div>
@endif
