<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>

<form action="/" class="formulario" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input 
            type="email"
            id="email"
            placeholder="Tu Email"
            name="email"
        >
    </div>

    <div class="campo">
        <label for="email">Password</label>
            <input 
                type="password"
                id="password"
                placeholder="Tu Password"
                name="password"
            >
    </div>

    <input type="submit" class="boton" value="Iniciar Sesión">
</form>

<div class="acciones">
    <a href="/crear-cuenta">¿Aún no tenes una cuenta? Crea una</a>
    <a href="/olvide">¿Olvidaste tu password?</a>
</div>