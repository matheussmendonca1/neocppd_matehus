<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Bem-vindo ao Dashboard</h1>
    <p><a href="/auth/logout">Sair</a></p>
    <?php if(session('role') == 'admin'): ?>
        <h2>Menu Admin</h2>
        <ul>
            <li><a href="#">Gerenciar Usuários</a></li>
            <li><a href="#">Configurações</a></li>
        </ul>
    <?php else: ?>
        <h2>Menu Usuário</h2>
        <ul>
            <li><a href="#">Ver Perfil</a></li>
            <li><a href="#">Configurações</a></li>
        </ul>
    <?php endif; ?>
</body>
</html>
