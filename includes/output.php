<?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $fetched = $output->fetch()): ?>
    <pre class="alert alert-info"><?php print $fetched ?></pre>
<?php endif ?>

