<!DOCTYPE html>
<html lang="es">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - Batidos Pitaya</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/global.css">

    <style>
        .error-404 {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-principal);
            color: white;
            text-align: center;
            padding: 2rem;
        }

        .error-content {
            max-width: 600px;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .error-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .error-description {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .error-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
        }

        .error-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .error-link:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-2px);
        }

        .error-link i {
            font-size: 1.5rem;
        }

        @media (min-width: 768px) {
            .error-links {
                flex-direction: row;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="error-404">
        <div class="error-content">
            <div class="error-code">404</div>
            <h1 class="error-title">Página no encontrada</h1>
            <p class="error-description">
                Lo sentimos, la página que buscas no existe o ha sido movida.
                Pero no te preocupes, tenemos muchas oportunidades laborales esperándote.
            </p>

            <div class="error-links">
                <a href="/" class="error-link">
                    <i class="bi bi-house-fill"></i>
                    Ir al inicio
                </a>
                <a href="/unete#vacantes" class="error-link">
                    <i class="bi bi-briefcase-fill"></i>
                    Ver vacantes
                </a>
            </div>

            <div class="mt-4">
                <p class="mb-2">¿Necesitas ayuda?</p>
                <p class="mb-0">
                    <i class="bi bi-envelope"></i> seleccion@batidospitaya.com |
                    <i class="bi bi-telephone"></i> +505 8852 0629
                </p>
            </div>
        </div>
    </div>
</body>

</html>