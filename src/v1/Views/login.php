<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="<?=$rootpath?>/assets/fomantic-ui/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="<?=$rootpath?>/assets/main.css">
  <link rel="stylesheet" href="<?=$rootpath?>/assets/toast-ui/toastui-editor.min.css" />  <script type="text/javascript" src="<?=$rootpath?>/assets/fomantic-ui/jquery-3.7.1.min.js"></script>
  <script type="text/javascript" src="<?=$rootpath?>/assets/fomantic-ui/semantic.min.js"></script>
  <script type="text/javascript" src="<?=$rootpath?>/assets/fomantic-ui/jquery.tablesort.min.js"></script>
  <script src="<?=$rootpath?>/assets/toast-ui/toastui-editor-all.min.js"></script>
  <title><?=$title?></title>
</head>
<body class="loginbg">

  <div class="ui container" style="height: 100%">
    <div class="ui middle aligned center aligned grid" style="height: 100%">
      <div class="column">
        <div class="ui placeholder segment">
          <div class="ui two column stackable center aligned grid">
            <div class="ui vertical divider">Or</div>
            <div class="middle aligned row">
              <div class="column">
                <div class="ui icon header">
                  <i class="sign in alternate icon"></i>
                  Sign in
                </div>
                <form method="post" class="ui form">
                  <div class="field">
                    <label>Login</label>
                    <input type="text" name="login">
                  </div>
                  <div class="field">
                    <label>Password</label>
                    <input type="password" name="password">
                  </div>
                  <div>
                    <button class="ui primary button" type="submit">Send</button>
                  </div>
                </form>
              </div>
              <div class="column">
                <div class="ui icon header">
                <i class="address card outline icon"></i>
                  Auto-login / SSO
                </div>
                <div class="ui primary button">
                  Login
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>