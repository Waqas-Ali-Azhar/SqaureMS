<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
$usersQuery = "SELECT * FROM users";
$userResult = $conn->query($usersQuery);
$assigneeOptions = [];
$bmOptions = [];
$developerOptions = [];
$testerOptions = [];
$estimationId = isset($_GET['id']) ? intval($_GET['id']) : '';

while ($row = mysqli_fetch_assoc($userResult)) {
  $assigneeOptions[] = ['id' => (int)$row['id'], 'name' => $row['full_name']];
  $name = $row['full_name'];
  $id = (int)$row['id'];

  if ($row['role_id'] == 11) { // BM
    $bmOptions[] = ['id' => $id, 'name' => $name];
  } elseif ($row['role_id'] == 4) { // Developer
    $developerOptions[] = ['id' => $id, 'name' => $name];
  } elseif ($row['role_id'] == 5) { // Tester
    $testerOptions[] = ['id' => $id, 'name' => $name];
  }
}

?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- css -->
<link rel="stylesheet" href="/assets/dist/css/estimation-details.css">
<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-sm-12">
      <div class="content-wrapper">
        <div class="table-wrapper">
          <div class="table-space">
            <div class="table-scroll-wrapper">
              <form action="" id="milestoneForm">
                <!-- <input type="text" name = "task name" placeholder="task name"> -->
                <div class="container">
                  <button type="button" id="addMilestoneBtn" style="margin-bottom: 20px;"><b>+</b> Add Milestone</button>
                  <div id="milestoneList"></div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="subtask-menu">
          <div class="menu-top" style="width:100%; display:flex; padding:3px; margin-bottom:5px;">
            <div class="closeSubTaskMenu">x</div>
          </div>
          <div class="subTaskRow"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $('#milestoneForm').submit(function(e) {
    e.preventDefault();
  });
  const estimationId = "<?php echo $estimationId; ?>";
  const milestoneList = document.getElementById('milestoneList');
  const addMilestoneBtn = document.getElementById('addMilestoneBtn');

  function createMilestoneBlock(data = {}) {
    const block = document.createElement('div');
    block.className = 'milestone-block-wrapper'; // outer wrapper
    block.dataset.milestoneId = data.milestone_id || '';
    block.style.marginBottom = '20px';

    // Inner flex container for milestone fields
    const milestoneFields = document.createElement('div');
    milestoneFields.className = 'milestone-block';

    function createField(labelText, inputType, name, value = '', placeholder = '', width) {
      const fieldWrapper = document.createElement('div');
      fieldWrapper.className = 'milestone-field';

      const label = document.createElement('label');
      label.textContent = labelText;

      const input = document.createElement('input');
      input.type = inputType;
      input.name = name;
      if (placeholder) input.placeholder = placeholder;
      if (width) input.style.width = width;
      input.value = value;

      fieldWrapper.appendChild(label);
      fieldWrapper.appendChild(input);
      return input;
    }

    const idInput = createField('Milestone ID', 'text', 'milestone_id[]', data.milestone_id || '', '', '100px');
    idInput.readOnly = true;

    const nameInput = createField('Milestone Name', 'text', 'milestone_name[]', data.milestone_name || '', 'Name');

    const nextOrder = data.milestone_order || (milestoneList.children.length + 1);
    const orderInput = createField('Order', 'number', 'milestone_order[]', nextOrder, 'Order', '100px');
    orderInput.readOnly = true;

    const descInput = createField('Description', 'text', 'milestone_description[]', data.milestone_description || '', 'Description');
    const estimationInput = createField('Estimation ID', 'text', 'estimation_id[]', data.estimation_id || estimationId, '', '100px');
    estimationInput.readOnly = true;

    const saveBtn = document.createElement('i');
    saveBtn.className = 'fa-solid fa-floppy-disk save-icon';
    saveBtn.title = 'Save';
    saveBtn.style.color = 'green';
    saveBtn.style.cursor = 'pointer';

    const removeBtn = document.createElement('i');
    removeBtn.className = 'fa-solid fa-trash remove-icon';
    removeBtn.title = 'Remove';
    removeBtn.style.color = 'red';
    removeBtn.style.cursor = 'pointer';

    saveBtn.addEventListener('click', () => {
      const milestoneData = {
        id: idInput.value,
        name: nameInput.value,
        order: orderInput.value,
        description: descInput.value,
        estimation_id: estimationInput.value
      };
      document.dispatchEvent(new CustomEvent("saveMilestone", {
        detail: milestoneData
      }));
      if (idInput.value) {
        addTaskBtn.disabled = false;
      }
    });

    removeBtn.addEventListener('click', () => {
      const order = orderInput.value;
      document.dispatchEvent(new CustomEvent("removeMilestone", {
        detail: {
          id: idInput.value,
          order,
          block
        }
      }));
      block.remove();
      reorderMilestones();
    });

    // Append fields into flex row
    milestoneFields.appendChild(idInput.parentElement);
    milestoneFields.appendChild(nameInput.parentElement);
    milestoneFields.appendChild(orderInput.parentElement);
    milestoneFields.appendChild(descInput.parentElement);
    milestoneFields.appendChild(estimationInput.parentElement);
    milestoneFields.appendChild(saveBtn);
    milestoneFields.appendChild(removeBtn);

    // Task section (below fields)
    const taskSection = document.createElement('div');
    taskSection.className = 'task-section';
    taskSection.style.marginTop = '10px';

    const taskList = document.createElement('div');
    taskList.className = 'task-list';

    const addTaskBtn = document.createElement('button');
    addTaskBtn.type = 'button';
    addTaskBtn.textContent = '+ Add Task';
    addTaskBtn.className = 'add-task-btn';
    addTaskBtn.style.marginTop = '10px';
    addTaskBtn.disabled = !data.milestone_id;

    function addTaskRow(taskData = {}) {
      const taskRow = document.createElement('div');
      taskRow.setAttribute('data-task-id', taskData.id || '');
      taskRow.className = 'task-row taskfield';
      taskRow.style.display = 'flex';
      taskRow.style.marginBottom = '5px';

      const isSaved = !!taskData.id;

      const taskIDInput = document.createElement('input');
      taskIDInput.type = 'number';
      taskIDInput.name = 'taskID';
      taskIDInput.className = 'taskID';
      taskIDInput.readOnly = true;
      taskIDInput.value = taskData.id || '';

      const taskNameInput = document.createElement('input');
      taskNameInput.type = 'text';
      taskNameInput.name = 'task_name[]';
      taskNameInput.className = 'taskname taskfield';
      taskNameInput.placeholder = 'Task Name';
      taskNameInput.value = taskData.task_name || '';
      taskNameInput.title = taskNameInput.value;

      const MiDfield = document.createElement('input');
      MiDfield.type = 'number';
      MiDfield.name = 'milestoneID';
      MiDfield.class = 'milestoneID taskfield';
      MiDfield.value = idInput.value;
      MiDfield.readOnly = true;

      const taskHoursInput = document.createElement('input');
      taskHoursInput.type = 'number';
      taskHoursInput.className = 'estHours taskfield ';
      taskHoursInput.name = 'task_hours[]';
      taskHoursInput.placeholder = 'Etd(hr)';
      taskHoursInput.value = taskData.task_hours || '';
      taskHoursInput.disabled = !isSaved;

      const devSelect = document.createElement('select');
      const devOptions = <?php echo json_encode($developerOptions); ?>;
      devSelect.name = 'dev-select[]';
      devSelect.className = 'dev-select';
      devSelect.multiple = true;
      devOptions.forEach(dev => {
        const option = document.createElement('option');
        option.value = dev.name;
        option.textContent = dev.name;
        devSelect.appendChild(option);
      });
      devSelect.disabled = !isSaved;
      const bizManSelect = document.createElement('select');
      const bmOptions = <?php echo json_encode($bmOptions); ?>;
      bizManSelect.name = 'bm-select[]';
      bizManSelect.className = 'bm-select';
      bizManSelect.multiple = true;
      bmOptions.forEach(bm => {
        const option = document.createElement('option');
        option.value = bm.name;
        option.textContent = bm.name;
        bizManSelect.appendChild(option);
      });
      bizManSelect.disabled = !isSaved;
      const testerSelect = document.createElement('select');
      const testerOptions = <?php echo json_encode($testerOptions); ?>;
      testerSelect.name = 'tester-select[]';
      testerSelect.className = 'tester-select';
      testerSelect.multiple = true;
      testerOptions.forEach(test => {
        const option = document.createElement('option');
        option.value = test.name;
        option.textContent = test.name;
        testerSelect.appendChild(option);
      });
      testerSelect.disabled = !isSaved;
      //------subtaskbtn-----------------
      const subtaskBtn = document.createElement('button');
      const taskId = taskRow.dataset.taskId;
      const subtaskMenu = document.querySelector('.subtask-menu');
      const subtaskRowContainer = document.querySelector('.subTaskRow');

      subtaskBtn.type = 'button';
      subtaskBtn.dataset.taskId = taskId;
      subtaskBtn.className = 'subTask';
      subtaskBtn.innerHTML = '>';
      subtaskBtn.title = 'Add sub task ';
      subtaskBtn.disabled = !isSaved;

      subtaskBtn.addEventListener('click', () => {
        if (subtaskMenu.style.display === 'none' || subtaskMenu.style.display === '') {
          subtaskMenu.style.display = 'block';
        }
        subtaskRowContainer.innerHTML = ""; // for empty container
        subtaskMenu.dataset.parentTaskId = taskId;
        createSubtaskRow(taskId);
      });

      //-----------subtaskbtn/end------------------
      //  collect ALL fields from one task row
      function getTaskRowData(taskRow) {
        return {
          taskId: taskRow.dataset.taskId || null,
          milestoneId: $(taskRow).find('input[name="milestoneID"]').val(),
          taskName: $(taskRow).find('input[name="task_name[]"]').val(),
          taskHours: $(taskRow).find('input[name="task_hours[]"]').val(),
          developers: $(taskRow).find('.dev-select').val() || [],
          businessManagers: $(taskRow).find('.bm-select').val() || [],
          testers: $(taskRow).find('.tester-select').val() || []
        };
      }

      // ------New trigger function-------
      function triggerTaskChange(taskRow, action) {
        const rowData = getTaskRowData(taskRow);

        // Send to custom event (if want to listen globally)
        document.dispatchEvent(new CustomEvent("taskFieldChange", {
          detail: {
            action,
            rowData
          }
        }));

        $.ajax({
          url: "save-task.php",
          method: "POST",
          data: rowData,
          success: function(res) {
            console.log("Task saved:", res);
          },
          error: function(err) {
            console.error("Error saving task:", err);
          }
        });
      }
      //  taskNameInput.addEventListener("blur", () => triggerTaskChange(taskRow, "update"));
      taskNameInput.addEventListener("blur", () => {
        triggerTaskChange(taskRow, "update");
        if (taskNameInput.value.trim() !== "") {
          location.reload();
        }
      });
      taskHoursInput.addEventListener("blur", () => triggerTaskChange(taskRow, "update"));

      $(devSelect).on("change", () => triggerTaskChange(taskRow, "update"));
      $(bizManSelect).on("change", () => triggerTaskChange(taskRow, "update"));
      $(testerSelect).on("change", () => triggerTaskChange(taskRow, "update"));

      //-----------------------------

      taskRow.appendChild(taskIDInput);
      taskRow.appendChild(taskNameInput);
      taskRow.appendChild(MiDfield);
      taskRow.appendChild(taskHoursInput);
      taskRow.appendChild(devSelect);
      taskRow.appendChild(bizManSelect);
      taskRow.appendChild(testerSelect);
      taskRow.appendChild(subtaskBtn);
      taskList.appendChild(taskRow);
      //----------------
      $('.dev-select').select2({
        placeholder: 'Select Developer'
      });
      $(bizManSelect).select2({
        placeholder: 'Select Business Manager',
        width: 'resolve'
      });
      $(testerSelect).select2({
        placeholder: 'Select Tester',
        width: 'resolve'
      });
      // (targetTaskList || taskList).appendChild(taskRow);
      if (taskData.developers && taskData.developers.length) {
        $(devSelect).val(taskData.developers).trigger('change');
      }
      if (taskData.business_manager && taskData.business_manager.length) {
        $(bizManSelect).val(taskData.business_manager).trigger('change');
      }
      if (taskData.testers && taskData.testers.length) {
        $(testerSelect).val(taskData.testers).trigger('change');
      }
    }

    addTaskBtn.addEventListener('click', () => addTaskRow());

    taskSection.appendChild(taskList);
    taskSection.appendChild(addTaskBtn);

    block.appendChild(milestoneFields);
    block.appendChild(taskSection);

    milestoneList.appendChild(block);
    reorderMilestones();
    block.addTaskRow = addTaskRow;
    return block;
  }

  addMilestoneBtn.addEventListener('click', createMilestoneBlock);
  //------------load milestone------------
  function loadMilestones() {
    fetch(`get-milestones.php?estimation_id=${estimationId}`)
      .then(res => res.json())
      .then(data => {
        data.forEach(m => {
          const milestoneBlock = createMilestoneBlock({
            milestone_id: m.id,
            milestone_name: m.name,
            milestone_order: m.milestone_order,
            milestone_description: m.description,
            estimation_id: m.estimation_id
          });

          // now fetch tasks for this milestone
          fetch(`get-task.php?milestone_id=${m.id}`)
            .then(r => r.json())
            .then(tasks => {
              console.log("Tasks response for milestone", m.id, tasks);
              tasks.forEach(t => {
                milestoneBlock.addTaskRow(t);
              });
            });
        });
      })

      .catch(err => console.error('Error loading milestones:', err));
  }

  // Load saved milestones when the page is ready
  document.addEventListener('DOMContentLoaded', loadMilestones);
  //-------------reorderMilestones---------------
  function reorderMilestones() {
    const blocks = Array.from(document.querySelectorAll('.milestone-block'));

    blocks.forEach((block, index) => {
      const orderInput = block.querySelector('input[name="milestone_order[]"]');
      if (orderInput) {
        orderInput.value = index + 1;
      }
    });

    return blocks.length + 1; //  For returning the next available order number
  }
  //===========subtask========
  $('.closeSubTaskMenu').click(function() {
    $('.subtask-menu').hide();
  });
  const taskID = '';
  const menu = document.querySelector('.subTaskRow');
  const btn = document.createElement('button');
  btn.textContent = "New Button";
  menu.appendChild(btn);
  //--------subtaskmenu----------
  function createSubtaskRow(parentTaskId) {
    const subtaskRowContainer = document.querySelector('.subTaskRow');
    subtaskRowContainer.innerHTML = ""; // reset rows when opening menu

    // --- Add Subtask Button ---
    const addSubtaskBtn = document.createElement('button');
    addSubtaskBtn.type = 'button';
    addSubtaskBtn.textContent = '+ Add Subtask';
    addSubtaskBtn.className = 'add-subtask-btn';
    addSubtaskBtn.style.marginTop = '10px';

    // click event: add new row
    addSubtaskBtn.addEventListener('click', () => {
      const row = buildSubtaskRow(parentTaskId);
      subtaskRowContainer.insertBefore(row, addSubtaskBtn);
    });

    // first row
    const firstRow = buildSubtaskRow(parentTaskId);
    subtaskRowContainer.appendChild(firstRow);

    // append the button last
    subtaskRowContainer.appendChild(addSubtaskBtn);
  }

  function buildSubtaskRow(parentTaskId) {
    const subtaskRow = document.createElement('div');
    subtaskRow.className = 'subtask-row';
    subtaskRow.setAttribute('data-task-id', parentTaskId);

    const sub_id = document.createElement('input');
    sub_id.type = 'number';
    sub_id.placeholder = 'id';
    sub_id.readOnly = true;

    const nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.name = 'subtask_name[]';
    nameInput.placeholder = 'Subtask Name';

    const parent_id = document.createElement('input');
    parent_id.type = 'number';
    parent_id.value = parentTaskId;
    parent_id.readOnly = true;

    const descInput = document.createElement('input');
    descInput.type = 'text';
    descInput.name = 'subtask_description[]';
    descInput.placeholder = 'Description';

    const devSelect = document.createElement('select');
    devSelect.name = 'subtask_dev[]';
    devSelect.className = 'subtask-dev-select';
    const devOptions = <?php echo json_encode($developerOptions); ?>;
    devOptions.forEach(dev => {
      const option = document.createElement('option');
      option.value = dev.id;
      option.textContent = dev.name;
      devSelect.appendChild(option);
    });

    const saveBtn = document.createElement('button');
    saveBtn.textContent = 'Save';
    saveBtn.type = 'button';
    saveBtn.className = 'save-subtask-btn';

    subtaskRow.appendChild(sub_id);
    subtaskRow.appendChild(nameInput);
    subtaskRow.appendChild(parent_id);
    subtaskRow.appendChild(descInput);
    subtaskRow.appendChild(devSelect);

    return subtaskRow;
    // -------------
    function getSubtaskRowData(row) {
      return {
        subtask_id: $(row).find('input[placeholder="id"]').val(),
        parent_task_id: $(row).find('input[type="number"][readonly]').val(),
        subtask_name: $(row).find('input[name="subtask_name[]"]').val(),
        subtask_description: $(row).find('input[name="subtask_description[]"]').val(),
        subtask_dev: $(row).find('.subtask-dev-select').val() || []
      };
    }

    function triggerSubtaskChange(subtaskRow, action) {
      const rowData = getSubtaskRowData(subtaskRow);

      // global event if you want to listen
      document.dispatchEvent(new CustomEvent("subtaskFieldChange", {
        detail: {
          action,
          rowData
        }
      }));

      $.ajax({
        url: "save-subtask.php",
        method: "POST",
        data: rowData,
        success: function(res) {
          console.log("Subtask saved:", res);
        },
        error: function(err) {
          console.error("Error saving subtask:", err);
        }
      });
    }
    // When user leaves input
    nameInput.addEventListener("blur", () => triggerSubtaskChange(subtaskRow, "update"));
    descInput.addEventListener("blur", () => triggerSubtaskChange(subtaskRow, "update"));

    // When dev select changes
    $(devSelect).on("change", () => triggerSubtaskChange(subtaskRow, "update"));


  }
</script>

<script src="/assets/dist/js/estimation-details.js"></script>
<?php include('includes/footer.php'); ?>