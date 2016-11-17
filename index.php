<?php

require __DIR__ . "/vendor/autoload.php";

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

putenv("COMPOSER_HOME=" . getcwd() . "/vendor/bin/composer");

$messageType = "";
$messageText = "";

$input = null;
$output = new BufferedOutput();

register_shutdown_function(function() use (&$messageType, &$messageText, $output) {
    $last = error_get_last();

    if ($last["type"] === E_ERROR) {
        print '<link rel="stylesheet" href="assets/css/bootstrap.css" />';
        print '<link rel="stylesheet" href="assets/css/app.css" />';
        print '<div class="alert alert-danger pre-line">';
        print $output->fetch();
        print '</div>';
    }
});

chdir(__DIR__);

if (isset($_GET["path"])) {
    chdir(urldecode($_GET["path"]));
}

$files = new DirectoryIterator(getcwd());

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $application = new Application();
    $application->setAutoExit(false);

    if (isset($_POST["install"])) {
        $input = new ArrayInput(["command" => "install"]);
    }

    if (isset($_POST["update"])) {
        $input = new ArrayInput(["command" => "update"]);
    }

    if (isset($_POST["require"])) {
        if (empty($_POST["dependency"])) {
            $messageType = "danger";
            $messageText  = "You must enter a dependency name";
        } else {
            $input = new ArrayInput([
                "command" => "require",
                "packages" => [ $_POST["dependency"] ]
            ]);
        }
    }

    if (isset($_POST["remove"])) {
        if (empty($_POST["dependency"])) {
            $messageType = "danger";
            $messageText  = "You must enter a dependency name";
        } else {
            $input = new ArrayInput([
                "command" => "remove",
                "packages" => [ $_POST["dependency"] ]
            ]);
        }
    }

    if ($input) {
        $result = $application->run($input, $output);
    }

}

?><!doctype html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="assets/css/bootstrap.css" />
        <link rel="stylesheet" href="assets/css/app.css" />
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <span><?php print implode("</span><span>/", explode("/", getcwd())) ?></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Files
                        </div>
<!--                        <div class="">-->
                            <table class="panel-body table table-bordered table-striped">
                                <tbody>
                                <?php foreach($files as $file): ?>
                                    <tr class="file <?php $file->isDir() ? print "is-directory" : print "is-file" ?>">
                                        <td>
                                            <?php if ($file->isDir()): ?>
                                                <a href="?path=<?php print urlencode(realpath($file->getPathname())) ?>">
                                                    <?php print $file->getFilename() ?>
                                                </a>
                                            <?php else: ?>
                                                <?php print $file->getFilename() ?>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
<!--                        </div>-->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Project</div>
                        <div class="panel-body">
                            <?php if (isset($_POST["install"]) || isset($_POST["update"])): ?>
                                <?php require __DIR__ . "/includes/alert.php" ?>
                            <?php endif ?>
                            <form method="post">
                                <input class="btn btn-success" type="submit" name="install" value="install" />
                                <input class="btn btn-default" type="submit" name="update" value="update" />
                            </form>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Dependencies</div>
                        <div class="panel-body">
                            <?php if (isset($_POST["require"]) || isset($_POST["remove"])): ?>
                                <?php require __DIR__ . "/includes/alert.php" ?>
                            <?php endif ?>
                            <form method="post">
                                <div class="form-group">
                                    <input  class="form-control" type="text" id="dependency" name="dependency" value="<?php isset($_POST["dependency"]) ? print $_POST["dependency"] : print "" ?>" />
                                </div>
                                <input class="btn btn-success" type="submit" name="require" value="require" />
                                <input class="btn btn-default" type="submit" name="remove" value="remove" />
                            </form>
                            <?php if (isset($_POST["require"]) || isset($_POST["remove"])): ?>

                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php require __DIR__ . "/includes/output.php" ?>
                </div>
            </div>
        </div>
        <script src="assets/js/jquery.js"></script>
        <script src="assets/js/app.js"></script>
    </body>
</html>
