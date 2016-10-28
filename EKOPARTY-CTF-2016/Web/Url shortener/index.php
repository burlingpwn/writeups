<?php

include 'config.php';

if (isset($_GET['i']) && $_GET['i'] == 1) {
        header("Location: https://ctf.ekoparty.org");
        exit();
}
?>
<html lang="en" >
   <head>
      <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.0/angular-material.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-animate.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-aria.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-messages.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.0/angular-material.min.js"></script>
      <script type="text/javascript">
        angular.module('firstApplication', ['ngMaterial']);
      </script>
       <script type="text/javascript">
            var onloadCallback = function() {
                grecaptcha.render('CAPTCHA', {
                  'sitekey' : '6LeXbAsTAAAAAGxGr1KYPtg0gkEgDDgDZQOZx85G'
                });
            };
        </script>

          <style>
        md-whiteframe {  background: #fff;  margin: 20px; }
          </style>
   </head>
 <body ng-app="firstApplication" ng-cloak>

      <md-toolbar class="md-warn">
         <div class="md-toolbar-tools" >
            <h2 class="md-flex">URL Shortener</h2>
                <span flex></span>
                  <md-button>
                        About
                  </md-button>
         </div>
      </md-toolbar>
      <md-content flex layout-padding>

<md-whiteframe class="md-whiteframe-2dp" layout layout-align="center center">
<p>Are you sick of posting URLs in emails only to have it break when sent causing the recipient to have to cut and paste it back together? Then you've come to the right place. <br/>You can use any URL!! as long as the url is <b>ctf.ekoparty.org/</b></p>
</md-whiteframe>

<?php

function page_title($url) {
        $fp = get_data($url);
        if (!$fp)
            return "(no title)";
        $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
        if (!$res)
            return "(no title)";
        $title = preg_replace('/\s+/', ' ', $title_matches[1]);
        $title = trim($title);
                if ($title == "") $title = "(no title)";
        return $title;
}

function get_data($url) {
        require(".htflag.php");
        $url = escapeshellarg($url);
        $flag = escapeshellarg($flag);
        exec("wget -qO-  --user-agent $flag $url", $output);
        return implode("\r\n", $output);

}


$url = isset($_POST['url']) ? $_POST['url'] : "";

if ($url != "" && !empty($_POST['g-recaptcha-response']) ) {
        require('captcha/autoload.php');
        $recaptcha = new \ReCaptcha\ReCaptcha($secret_key);
        $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        echo '<md-whiteframe class="md-whiteframe-1dp" layout layout-align="center center">';

        if ($resp->isSuccess()) {

                $pu = parse_url($url);
                if (isset($pu["host"]) && isset($pu["scheme"])) {
                        if ($pu["host"] === "ctf.ekoparty.org" && ($pu["scheme"] === "http"||$pu["scheme"] === "https")) {
                                echo "<p>Accepted hostname <b ng-non-bindable>".htmlentities($pu["host"])."</b><br/><br/>";
                                $response = page_title($url);
                                echo "<b>Title:&nbsp;</b><span  ng-non-bindable>" . htmlentities($response)."</span><br/>";
                                echo "<b>Short Link:&nbsp;</b><a href='?i=1'>http://9a958a70ea8697789e52027dc12d7fe98cad7833.ctf.site:20000/?i=1</a></p>";
                        } else {
                                echo "<p>Error, hostname not allowed</p>&nbsp; <p><b ng-non-bindable>".htmlentities($pu["host"])."</b></p>";
                        }
                } else {
                        echo "<p>Invalid URL&nbsp;<b ng-non-bindable>".htmlentities($url)."</b></p>";
                }

        } else  {
                echo "<p>Invalid CAPTCHA&nbsp;</p>";
        }

        echo '</md-whiteframe>';

}
?>

<md-whiteframe class="md-whiteframe-1dp"   layout layout-align="center" >
        <form method="POST" action="" autocomplete=off>
                <md-input-container style="width: 350px">
                        <label>URL</label>
                        <input ng-model="user.url" type=url name=url ng-init="user.url='<?=addslashes(htmlentities($url));?>'" required>
                </md-input-container>


        <div id="CAPTCHA"></div>
        </div>
<br/>
        <md-button class="md-raised" type='submit'>Submit</md-button>

        </form>
</md-whiteframe>

<!-- /container -->
<script src="//www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

</md-content>
</body>
</html>
