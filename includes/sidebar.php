<?php
require_once __DIR__ . '/../config/database.php';
if (!$_SESSION) {
  session_start();
}

$access = $_SESSION['access'] ?? [];
$isAdmin = $_SESSION['is_admin'] ?? false;

$allowedBrands = [];
$allowedRoles = [];
$allowedEntities = [];

// Group permissions by brand
$brandWiseAccess = [];

if (!$isAdmin && !empty($access)) {
  foreach ($access as $item) {
    $brand = strtolower($item['brand_name']);

    $allowedBrands[] = $brand;

    $brandWiseAccess[$brand] = [
      'roles'    => array_map('strtolower', $item['role_names'] ?? []),
      'entities' => array_map('strtolower', $item['entity_names'] ?? [])
    ];

    $allowedRoles = array_merge($allowedRoles, array_map('strtolower', $item['role_names'] ?? []));
    $allowedEntities = array_merge($allowedEntities, array_map('strtolower', $item['entity_names'] ?? []));
  }

  $allowedBrands = array_unique($allowedBrands);
  $allowedRoles = array_unique($allowedRoles);
  $allowedEntities = array_unique($allowedEntities);
}
// echo '<pre>';
// print_r($allowedBrands);
// echo '</pre>';
// exit();

?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="index3.html" class="brand-link">
    <img src="/assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
      style="opacity: .8">
    <span class="brand-text font-weight-light">AdminLTE 3</span>
  </a>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="/assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo $_SESSION['auth']['username']; ?></a>
      </div>
      <?php
     
      
      echo "<!--";
      print_r($_SESSION);
      echo "-->";
      
      
      ?>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-item has-treeview menu-open">
          <a href="/index.php" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
            </p>
          </a>

        </li>
        <!-- <li class="nav-item has-treeview" id="content-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Users
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/show-users.php" class="nav-link" id="viewsheet_link">
                <i class="far fa-circle nav-icon"></i>
                <p>Show Users</p>
              </a>
            </li>
             <li class="nav-item">
              <a href="/add-user.php" class="nav-link" id="viewsheet_link">
                <i class="far fa-circle nav-icon"></i>
                <p>Add User</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview side-menu" id="categories-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Categories
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview" id="categories-links">
            <li class="nav-item">
              <a href="/master.php" class="nav-link" id="master_link">
                <i class="far fa-circle nav-icon"></i>
                <p>Master</p>
              </a>
            </li>


            <li class="nav-item">
              <a href="/general.php" class="nav-link" id="general_link">
                <i class="far fa-circle nav-icon"></i>
                <p>General</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/specific.php" class="nav-link" id="specific_link">
                <i class="far fa-circle nav-icon"></i>
                <p>Specific</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview" id="tools-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Tools
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/import.php" class="nav-link" id="importsheet_link">
                <i class="far fa-circle nav-icon"></i>
                <p>Import Sheet</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/import_data.php" class="nav-link" id="importdata_link">
                <i class="far fa-circle nav-icon"></i>
                <p>Import Data</p>
              </a>
            </li>

          </ul>
        </li>
        <li class="nav-item has-treeview" id="view-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              View
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/view_sheet.php" class="nav-link" id="viewsheet_link">
                <i class="far fa-circle nav-icon"></i>
                <p>View Sheet</p>
              </a>
            </li>
          </ul>
        </li>



        <li class="nav-item has-treeview" id="projects-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Projects
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/all_projects.php" class="nav-link">
                <i class="far fa-circle nav-icon" id="all_projects_link"></i>
                <p>All Projects</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/add-newproject.php" class="nav-link" id="add_newproject_link">
                <i class="far fa-circle nav-icon"></i>
                <p>Add New Projects</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/edit_projects.php" class="nav-link" id="add_newproject_link">
                <i class="far fa-circle nav-icon"></i>
                <p>Edit Projects</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview" id="content-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Content
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/content.php" class="nav-link" id="viewsheet_link">
                <i class="far fa-circle nav-icon"></i>
                <p>contents</p>
              </a>
            </li>
          </ul>
        </li> -->
        <!-- View -->
        <li class="nav-item has-treeview" id="view-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-eye"></i>
            <p>
              Views
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item" style="margin-left:23px;">
              <a href="/view_sheet.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>View Sheet</p>
              </a>
            </li>
          </ul>
        </li>
        <!-- Projects -->
        <li class="nav-item has-treeview" id="projects-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-folder"></i>
            <p>
              Projects
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/all_projects.php" class="nav-link" style="margin-left:24px;">
                <i class="far fa-circle nav-icon"></i>
                <p>All Projects</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/add-newproject.php" class="nav-link" style="margin-left:24px;">
                <i class="far fa-circle nav-icon"></i>
                <p>Add New Projects</p>
              </a>
            </li>
          </ul>
        </li>
        <!-- Categories -->
        <li class="nav-item has-treeview" id="categories-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-tags"></i>
            <p>
              Categories
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/master.php" class="nav-link" style="margin-left:24px;">
                <i class="far fa-circle nav-icon"></i>
                <p>Master</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/general.php" class="nav-link" style="margin-left:24px;">
                <i class="far fa-circle nav-icon"></i>
                <p>General</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/specific.php" class="nav-link" style="margin-left:24px;">
                <i class="far fa-circle nav-icon"></i>
                <p>Specific</p>
              </a>
            </li>
          </ul>
        </li>
        <?php if ($isAdmin ): ?>
          <li class="nav-item has-treeview" id="alldata-main">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-database"></i>
              <p>
                Settings
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">

              <!-- Users -->
              <li class="nav-item has-treeview" id="users-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-users"></i>
                  <p>
                    All Users
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item" style="margin-left:24px;">
                    <a href="/show-users.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Show Users</p>
                    </a>
                  </li>
                  <li class="nav-item" style="margin-left:24px;">
                    <a href="/add-user.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add User</p>
                    </a>
                  </li>
                  <li class="nav-item" style="margin-left:24px;">
                    <a href="/manage_roles.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Role Management</p>
                    </a>
                  </li>
                  <li class="nav-item" style="margin-left:24px;">
                    <a href="/manage_permissions.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Permission Management</p>
                    </a>
                  </li>
                </ul>
              </li>



              <!-- Tools -->
              <li class="nav-item has-treeview" id="tools-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-tools"></i>
                  <p>
                    Imports
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/import.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Import Sheet</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/import_data.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Import Data</p>
                    </a>
                  </li>
                </ul>
              </li>


            </ul>
          </li>
        <?php endif; ?>
        <!-- ======CRM======= -->
        <li class="nav-item has-treeview" id="crm-main">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-database"></i>
            <p>
              CRM
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">
            <!-- Leads -->
            <?php if ($isAdmin || in_array('lead', $allowedEntities)): ?>
              <li class="nav-item has-treeview" id="leads-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-tags"></i>
                  <p>
                    Leads
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="leads.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Leads</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Un Assigned</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Assigned</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/add_lead.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Lead</p>
                    </a>
                  </li>

                </ul>
              </li>
            <?php endif; ?>
            <!-- estimations -->
            <?php if ($isAdmin || in_array('estimation', $allowedEntities)): ?>
              <li class="nav-item has-treeview" id="estimations-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-tags"></i>
                  <p>
                    Estimations
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/estimations.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Estimations</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/add-estimation.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Estimation</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php endif; ?>
            <!-- Contracts -->
            <?php if ($isAdmin || in_array('contract', $allowedEntities)): ?>
              <li class="nav-item has-treeview" id="contracts-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-tags"></i>
                  <p>
                    Contracts
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/contracts.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Contracts</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Open Contracts</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/add_contract.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Contract</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php endif; ?>
            <!-- Assignments -->
            <?php if ($isAdmin || in_array('assignment', $allowedEntities)): ?>
              <li class="nav-item has-treeview" id="assignments-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-tags"></i>
                  <p>
                    Assignments
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/assignments.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Assignments</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Completed</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Under Progress</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="add-assignment.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Assignment</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php endif; ?>
            <!-- Tasks -->
            <?php if ($isAdmin || in_array('task', $allowedEntities)): ?>
              <li class="nav-item has-treeview" id="tasks-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-tags"></i>
                  <p>
                    Tasks
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/tasks.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Tasks</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Closed Tasks</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Open Tasks </p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/add-task.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Task</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php endif; ?>
            <!-- Clients -->
            <?php if ($isAdmin || in_array('client', $allowedEntities)): ?>
              <li class="nav-item has-treeview" id="clients-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-tags"></i>
                  <p>
                    Clients
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/clients.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Clients</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Active Clients</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/add_client.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Client</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php endif; ?>
            <!-- portfolio -->
            <?php if ($isAdmin || in_array('portfolio', $allowedEntities)): ?>
              <li class="nav-item has-treeview" id="portfolio-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-tags"></i>
                  <p>
                    Portfolio
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/portfolio.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Portfolios</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Completed </p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Portfolio</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php endif; ?>
            <!-- Brands -->
            <?php if ($isAdmin || in_array('brand', $allowedEntities)): ?>
              <li class="nav-item has-treeview" id="brands-main">
                <a href="#" class="nav-link" style="margin-left:20px;">
                  <i class="nav-icon fas fa-tags"></i>
                  <p>
                    Brands
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/brands.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>All Brands</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Active Brands</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="add-brand.php" class="nav-link" style="margin-left:24px;">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Brand</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php endif; ?>


            <!-- projects -->
            <li class="nav-item has-treeview" id="estimations-main">
              <a href="#" class="nav-link" style="margin-left:20px;">
                <i class="nav-icon fas fa-tags"></i>
                <p>
                  Projects
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="projects.php" class="nav-link" style="margin-left:24px;">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Projects</p>
                  </a>
                </li>
              </ul>
            </li>
            <!-- .. -->
            <!-- plugins -->
            <li class="nav-item has-treeview" id="estimations-main">

              <a href="#" class="nav-link" style="margin-left:20px;">
                <i class="nav-icon fas fa-tags"></i>
                <p>
                  Plugins
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/add-plugin.php" class="nav-link" style="margin-left:24px;">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Add a Plugin</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="plugins.php" class="nav-link" style="margin-left:24px;">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Plugins</p>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <!-- ============= -->
      </ul>
    </nav>
  </div>
</aside>



<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="index3.html" class="brand-link">
    <img src="assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
      style="opacity: .8">
    <span class="brand-text font-weight-light">AdminLTE 3</span>
  </a>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block">Alexander Pierce</a>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  
        <li class="nav-item has-treeview menu-open">
          <a href="#" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="./index.html" class="nav-link active">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard v1</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./index2.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard v2</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./index3.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard v3</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="pages/widgets.html" class="nav-link">
            <i class="nav-icon fas fa-th"></i>
            <p>
              Widgets
              <span class="right badge badge-danger">New</span>
            </p>
          </a>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-copy"></i>
            <p>
              Layout Options
              <i class="fas fa-angle-left right"></i>
              <span class="badge badge-info right">6</span>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="pages/layout/top-nav.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Top Navigation</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/layout/boxed.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Boxed</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/layout/fixed-sidebar.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Fixed Sidebar</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/layout/fixed-topnav.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Fixed Navbar</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/layout/fixed-footer.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Fixed Footer</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/layout/collapsed-sidebar.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Collapsed Sidebar</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Subject
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="./master.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Master</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./view-master.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>View Master</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="pages/charts/flot.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>General</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/charts/inline.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Specific</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Charts
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="pages/charts/chartjs.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>ChartJS</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/charts/flot.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Flot</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/charts/inline.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Inline</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-tree"></i>
            <p>
              UI Elements
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="pages/UI/general.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>General</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/icons.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Icons</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/buttons.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Buttons</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/sliders.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Sliders</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/modals.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Modals & Alerts</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/navbar.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Navbar & Tabs</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/timeline.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Timeline</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/ribbons.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Ribbons</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>
              Forms
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="pages/forms/general.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>General Elements</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/forms/advanced.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Advanced Elements</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/forms/editors.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Editors</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
              Tables
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="pages/tables/simple.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Simple Tables</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/tables/data.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>DataTables</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/tables/jsgrid.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>jsGrid</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-header">EXAMPLES</li>
        <li class="nav-item">
          <a href="pages/calendar.html" class="nav-link">
            <i class="nav-icon far fa-calendar-alt"></i>
            <p>
              Calendar
              <span class="badge badge-info right">2</span>
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="pages/gallery.html" class="nav-link">
            <i class="nav-icon far fa-image"></i>
            <p>
              Gallery
            </p>
          </a>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon far fa-envelope"></i>
            <p>
              Mailbox
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="pages/mailbox/mailbox.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Inbox</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/mailbox/compose.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Compose</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/mailbox/read-mail.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Read</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-book"></i>
            <p>
              Pages
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="pages/examples/invoice.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Invoice</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/profile.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Profile</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/e_commerce.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>E-commerce</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/projects.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/project_add.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Project Add</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/project_edit.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Project Edit</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/project_detail.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Project Detail</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/contacts.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Contacts</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon far fa-plus-square"></i>
            <p>
              Extras
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="pages/examples/login.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Login</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/register.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Register</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/forgot-password.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Forgot Password</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/recover-password.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Recover Password</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/lockscreen.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Lockscreen</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/legacy-user-menu.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Legacy User Menu</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/language-menu.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Language Menu</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/404.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Error 404</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/500.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Error 500</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/pace.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Pace</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/examples/blank.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Blank Page</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="starter.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Starter Page</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-header">MISCELLANEOUS</li>
        <li class="nav-item">
          <a href="https://adminlte.io/docs/3.0" class="nav-link">
            <i class="nav-icon fas fa-file"></i>
            <p>Documentation</p>
          </a>
        </li>
        <li class="nav-header">MULTI LEVEL EXAMPLE</li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="fas fa-circle nav-icon"></i>
            <p>Level 1</p>
          </a>
        </li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-circle"></i>
            <p>
              Level 1
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Level 2</p>
              </a>
            </li>
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>
                  Level 2
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Level 3</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Level 3</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Level 3</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Level 2</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="fas fa-circle nav-icon"></i>
            <p>Level 1</p>
          </a>
        </li>
        <li class="nav-header">LABELS</li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon far fa-circle text-danger"></i>
            <p class="text">Important</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon far fa-circle text-warning"></i>
            <p>Warning</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon far fa-circle text-info"></i>
            <p>Informational</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside> -->