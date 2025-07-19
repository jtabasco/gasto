
<style>
.footer-fixed {
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    text-align: center;
    padding: 12px 0 8px 0;
    font-size: 1rem;
    z-index: 999;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.08);
}
.footer-fixed a {
    color: #fff;
    text-decoration: underline;
    margin: 0 8px;
    font-weight: 500;
}
.footer-fixed a:hover {
    color: #ffd700;
}
@media (max-width: 600px) {
    .footer-fixed {
        font-size: 0.95rem;
        padding: 10px 0 6px 0;
    }
}
</style>

<footer class="footer-fixed">
    <span>Sistema de Gestión de Gastos &copy; <?php echo date('Y'); ?> | Colorado</span>
    <span> | </span>
    <span>Email: <a href="mailto:jtabasco41@gmail.com">jtabasco41@gmail.com</a></span>
    <span> | </span>
    <span>Tel: <a href="tel:+17202034996">+1 720 203 4996</a></span>
</footer>