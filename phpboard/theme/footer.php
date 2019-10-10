    <!-- Footer -->
    <footer class="py-5 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; <?php print date("Y"); ?>, <a href="https://github.com/myaghobi/php-board">PHP-Board</a>.</p>
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