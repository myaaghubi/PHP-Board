    <!-- Footer -->
    <footer class="page-footer font-small mb-5">
        <div class="container footer-copyright text-center">
            <p class="m-0 text-center">Copyright &copy; <?php print date("Y"); ?>, <a href="https://github.com/myaghobi/php-board">PHP-Board</a>.</p>
        </div>
        <!-- /.container -->
    </footer>

    <?php
    if (!empty($jsList)) {
        if (is_array($jsList))
            foreach ($jsList as $item) {
                print '<script src="theme/assets/js/' . $item . '.js"></script>';
            } else
            print '<script src="theme/assets/js/' . $item . '.js"></script>';
    }
    ?>
    </body>

    </html>
