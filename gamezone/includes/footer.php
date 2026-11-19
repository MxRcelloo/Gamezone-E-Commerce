</main>
<?php $rootPath = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') ? '../' : ''; ?>
<footer class="site-footer mt-5">
    <div class="container footer-inner">
        <div><a class="brand" href="<?= $rootPath ?>index.php"><span class="brand-mark">G</span> GAME<span>ZONE</span></a><p>Seu próximo nível começa aqui.</p></div>
        <div class="footer-note">Projeto acadêmico de e-commerce · Pagamento simulado</div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $rootPath ?>assets/js/app.js"></script>
</body>
</html>
