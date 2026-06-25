<?php
// layout_talento/footer.php
// Plantilla de pie de página compartida para el portal de Talento de Batidos Pitaya
?>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="footer-title">Batidos Pitaya</h5>
                    <p class="footer-text">
                        Energía natural, hábitos positivos y experiencia WOW.
                    </p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="footer-title">Contacto</h5>
                    <ul class="footer-links">
                        <li><i class="bi bi-envelope"></i> <span class="email-protected" data-user="seleccion"
                                data-domain="batidospitaya.com"></span></li>
                        <li><i class="bi bi-telephone"></i> +505 8852 0629</li>
                        <!-- URL de búsqueda general en Google Maps — muestra todas las sucursales y sugiere la más cercana al usuario -->
                        <li><i class="bi bi-geo-alt"></i> <a href="https://www.google.com/maps/search/Batidos+Pitaya+Nicaragua/"
                                target="_blank" rel="noopener noreferrer" class="text-white">Ver ubicaciones</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-title">Síguenos</h5>
                    <div class="social-links">
                        <a href="https://www.facebook.com/BatidosPitaya" target="_blank" rel="noopener noreferrer"
                            class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/batidospitaya/" target="_blank" rel="noopener noreferrer"
                            class="social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.linkedin.com/company/batidospitaya/posts/?feedView=all" target="_blank"
                            rel="noopener noreferrer" class="social-link" title="LinkedIn"><i
                                class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; Batidos Pitaya. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Modal de Detalle de Plaza -->
    <div class="modal fade" id="modalDetallePlaza" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Detalle de la Plaza</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalContenido">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnPostularModal">
                        <i class="bi bi-send"></i> Postular a esta plaza
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js?v=<?php echo time(); ?>"></script>

    <!-- Schema.org JobPosting -->
    <script type="application/ld+json" id="schemaJobPostings">
        {
            "@context": "https://schema.org/",
            "@type": "Organization",
            "name": "Batidos Pitaya",
            "url": "https://batidospitaya.com",
            "logo": "https://talento.batidospitaya.com/assets/img/logo.png",
            "description": "Disfrutá los mejores batidos de pura fruta. Energía natural, hábitos positivos y experiencia WOW."
        }
    </script>

</body>

</html>
