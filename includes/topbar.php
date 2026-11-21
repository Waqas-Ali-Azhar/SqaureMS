  <?php

  $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $parsed_url = parse_url($uri_path);
 $path = $parsed_url['path'];
 $segments = explode('/', ltrim($path, '/'));
 $urlpart = '';



$headings = array(
    '/master.php'   => 'Master Categories',
    '/general.php'  => 'General Categories',
    '/specific.php' => 'Specific Categories',
    '/index.php'    => 'Dashboard'
    
);

// // Set default to basename if not in headings
// $heading = isset($headings[$uri_path]) ? $headings[$uri_path] : ucfirst(basename($uri_path, ".php"));
// if(strpos($uri_path,'/content')){
//    $urlpart = $segments[1];
//   $heading = $urlpart;
// }
// // Format heading nicely
// $heading = str_replace(['_', '-'], ' ', $heading);
// // echo '<pre>';
// // print_r($segments[1]);
// // echo '</pre>';
// // exit();
// If exact match in heading map, use it
if (isset($headings[$uri_path])) {
    $heading = $headings[$uri_path];
}

// Check for `/specific/` in path
elseif (in_array('specific', $segments)) {
    $key = array_search('specific', $segments);
    if (isset($segments[$key + 1])) {
        $urlpart = basename($segments[$key + 1], '.php');
        $heading = ucfirst(str_replace(['_', '-'], ' ', $urlpart)) . ' Category';
    }
}

// Default fallback: use the file name
else {
    $basename = basename($uri_path, '.php');
    $heading = ucfirst(str_replace(['_', '-'], ' ', $basename));
}

  // if (!empty($headings[$required_menu])) {
  //   $heading = $headings[$required_menu];
  // } else {
  //   $heading = $required_menu;
  // }

  ?>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light nav-bar-dashboard">
    <!-- Left navbar links -->
    <div class="main-navbar-nav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>

      </ul>
      <div class="dashboard-text">

        <h1 class="text-dark"><?php echo $heading; ?></h1>
      </div>
    </div>


    <!-- SEARCH FORM -->
    <form class="form-inline from-search-name ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
        <div class="">
            <button><a href="/form.php">Log Out</a></button>
        </div>
      </div>
    </form>

    <!-- <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <div class="media">
              <img src="assets/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <div class="media">
              <img src="assets/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <div class="media">
              <img src="assets/dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul> -->
  </nav>