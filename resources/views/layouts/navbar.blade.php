<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item">Gamdle Royale</a>
    </div>

    <div class="navbar-end">
        <div class="navbar-item">
            <div class="buttons">
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button type="submit" class="button is-danger">
                            <strong>Se déconnecter</strong>
                        </button>
                    </form>
                @else
                    <a href="{{ route('auth.discord') }}" class="button is-discord">
                        <strong>Connexion avec Discord</strong>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
