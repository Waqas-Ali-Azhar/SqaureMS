// function previewThumbnail(event) {
//     const input = event.target;
//     const preview = document.getElementById('thumbnailPreview');
//     if (input.files && input.files[0]) {
//         const reader = new FileReader();
//         reader.onload = function(e) {
//             preview.src = e.target.result;
//         };
//         reader.readAsDataURL(input.files[0]);
//     }
// }

// function removeImage() {
//     const input = document.getElementById('thumbnail');
//     const preview = document.getElementById('thumbnailPreview');
//     input.value = "";
//     preview.src = "./assets/img/icon-1.png";
// }
//======dropdown start=======
function bindMyEvents() {

}
$(document).ready(function () {

  $(".multiselect").each(function (index, select) {
    let select_id = $(select).attr("id");
    let selectedValues = [];

    $("#" + select_id + " option:selected").each(function () {
      selectedValues.push($(this).val());
    });

    let select_options = "";
    $("#" + select_id + " option").each(function () {
      const val = $(this).val();
      const isChecked = selectedValues.includes(val) ? "checked" : "";
      select_options +=
        "<li><div class='checkbox'><input type='checkbox' name='single_option' class='single_option' value='" +
        val +
        "' " +
        isChecked +
        ">" +
        val +
        "</div></li>";
    });

    let select_html =
      ` <div class="multiselect-main-wrapper" data-id="` +
      select_id +
      `">
        <div class="dropdown-button" data-id="` +
      select_id +
      `">
        <span class="multiselect-selected-text" data-id="` +
      select_id +
      `">Select</span>
        <b class="caret"><i class="fa-duotone fa-solid fa-chevron-down"></i></b>
        </div>
        <div class="multiselect-container">
            <input type="text"  placeholder="search.." class="search-input"> 
            <ul class="ulList" data-id="` +
      select_id +
      `-dropdown">
                <li style="border-bottom:1px solid grey;">
                    <div class='checkbox'>
                            <input type='checkbox'  class='select_all' name="select_all" )>All 
                    </div>
                </li>
                ` +
      select_options +
      `
            </ul>
            <input type='button'  class='multiselect-submit-btn ' value='SEARCH'>
        </div>
    </div>`;
    $(select).after(select_html);
  });

  //---------------------------
  $(".dropdown-button").click(function (event) {
    event.stopPropagation();
    var dropdown = $(this).siblings(".multiselect-container");
    $(".multiselect-container").not(dropdown).hide();
    dropdown.toggle();
  });

  //--------form submit if checked and also uncheck if checked----
  //   $(document).click(function (e) {
  //   $(".multiselect-container").each(function () {
  //     const container = $(this);
  //     const button = container.find(".multiselect-submit-btn");

  //     // If clicked outside both the container and its submit button
  //     if (
  //       !container.is(e.target) &&
  //       container.has(e.target).length === 0 &&
  //       !button.is(e.target) &&
  //       button.has(e.target).length === 0
  //     ) {
  //       if (container.is(":visible")) {
  //         button.closest("form")[0].submit(); // Submit related form
  //         container.hide(); // Hide dropdown
  //       }
  //     }
  //   });
  // });
  $(document).click(function (e) {
    $(".multiselect-container").each(function () {
      const container = $(this);
      const button = container.find(".multiselect-submit-btn");
      const form = container.closest("form");

      const hasFilterInput = document.getElementById("filters-applied");
      const hadFiltersApplied = hasFilterInput && hasFilterInput.value === "1";

      if (
        !container.is(e.target) &&
        container.has(e.target).length === 0 &&
        !button.is(e.target) &&
        button.has(e.target).length === 0 &&
        !$(e.target).closest('tr.dd-row').length //  Prevent auto-submit unless in filter row
      ) {
        if (container.is(":visible")) {
          let hasSelectedNow = false;

          form.find("tr.dd-row select.multiselect").each(function () {
            if ($(this).val() && $(this).val().length > 0) {
              hasSelectedNow = true;
              return false; // break
            }
          });

          // ✅ Submit if:
          // - There WERE filters (based on PHP POST)
          // - AND now there's at least one selection (OR one was removed)
          if (hadFiltersApplied || hasSelectedNow) {
            form[0].submit();
          }

          container.hide(); // Always hide dropdown
        }
      }
    });
  });
  //----------------------------------
  // checkbox
  $(".checkbox").on("click", function (e) {
    if ($(e.target).is("input")) return;

    var thisCheck = $(this).find("input");
    thisCheck.prop("checked", !thisCheck.prop("checked")).trigger("change");
  });

  $(".single-option").on("change", function () {
    var checkedcount = $(".single-option", ":checked").length;
  });
  //select all check:
  $(".select_all").change(function (e) {
    var select_id = $(this)
      .parent()
      .parent()
      .parent()
      .parent()
      .parent()
      .attr("data-id");

    if ($(this).is(":checked")) {
      $("#" + select_id + " option").each(function () {
        $(this).attr("selected", true);
      });
      $(this)
        .parent()
        .parent()
        .parent()
        .parent()
        .find(".single_option")
        .prop("checked", true);
      $("#" + select_id).trigger("change");
    } else {
      $("#" + select_id + " option").each(function () {
        $(this).attr("selected", false);
      });
      $(this)
        .parent()
        .parent()
        .parent()
        .parent()
        .find(".single_option")
        .prop("checked", false);
      $("#" + select_id).trigger("change");
    }
  });

  //dropdown search
  $(".search-input").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $(this)
      .siblings()
      .find("li")
      .filter(function () {
        $(this)
          .not($(".search-input").parent())
          .toggle($(this).text().toLowerCase().indexOf(value) > -1);
      });
  });

  //multiselect sync function
  $(".single_option").change(function (e) {
    e.stopPropagation();
    var value = $(this).val();
    var select_id = $(this)
      .parent()
      .parent()
      .parent()
      .parent()
      .parent()
      .attr("data-id");
    if ($(this).is(":checked")) {
      $("#" + select_id + " option").each(function (index, option) {
        if ($(this).val() == value) {
          $(option).prop("selected", true);
          $("#" + select_id).trigger("change");
        }
      });
    } else {
      $("#" + select_id + " option").each(function () {
        if ($(this).val() == value) {
          $(this).prop("selected", false);
          $("#" + select_id).trigger("change");
        }
      });
    }
  });
  //submit button function
  $(".multiselect-submit-btn").click(function (e) {
    e.preventDefault();
    this.form.submit();
    $(".multiselect-container").hide();
  });

  //-------------remove labels of selected values-----------
  $(document).on("click", ".remove-tag", function () {
    const tag = $(this).closest(".selected-tag");
    const value = tag.data("value");
    // const all_values = value.split(",");
    const all_values = (typeof value === 'string') ? value.split(",") : value;


    console.log(all_values);
    const select_id = tag.data("select-id");

    // Uncheck the matching checkbox in dropdown
    $(`.multiselect-main-wrapper[data-id='${select_id}'] .single_option`).each(function () {
      if (Array.isArray(all_values)) {
        if (all_values.includes($(this).val())) {
          $(this).prop("checked", false);
        }
      }
      else {
        if ($(this).val() == value) {
          $(this).prop("checked", false);
        }
      }
    });

    // Unselect the option in the hidden <select>
    $(`#${select_id} option`).each(function () {
      if ($(this).val() == value) {
        $(this).prop("selected", false);
      }

      if (Array.isArray(all_values) && all_values.includes($(this).val())) {
        $(this).prop("selected", false);
      }

    });

    // Trigger change on <select> (if needed)
    $(`#${select_id}`).trigger("change");

    // Remove tag from UI
    tag.remove();

    // Submit the form
    $("#data-form").submit();
  });

});
//=========dropdown end==================
document.addEventListener("DOMContentLoaded", function () {
  // Slug generator
  function generateSlug(text) {
    return (
      "/" +
      text
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, "-") // Replace non-alphanumerics with -
        .replace(/^-+|-+$/g, "")
    ); // Trim hyphens
  }

  const nameInput = document.getElementById("name");
  const slugInput = document.getElementById("slug");
  const thumbnailInput = document.getElementById("thumbnail");
  const thumbnailPreview = document.getElementById("thumbnailPreview");

  if (nameInput && slugInput) {
    // Update slug live
    nameInput.addEventListener("input", function () {
      slugInput.value = generateSlug(this.value);
    });

    // Trigger slug on load (if name has value)
    slugInput.value = generateSlug(nameInput.value);
  }
  // === Image 1 Preview ===
  const thumbnailInput1 = document.getElementById("thumbnail1");
  const thumbnailPreview1 = document.getElementById("thumbnailPreview1");

  if (thumbnailInput1 && thumbnailPreview1) {
    thumbnailInput1.addEventListener("change", function (event) {
      const reader = new FileReader();
      reader.onload = function () {
        thumbnailPreview1.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    });
  }

  // === Image 2 Preview ===
  const thumbnailInput2 = document.getElementById("thumbnail2");
  const thumbnailPreview2 = document.getElementById("thumbnailPreview2");

  if (thumbnailInput2 && thumbnailPreview2) {
    thumbnailInput2.addEventListener("change", function (event) {
      const reader = new FileReader();
      reader.onload = function () {
        thumbnailPreview2.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    });
  }

  window.removeImage1 = function () {
    if (thumbnailInput1 && thumbnailPreview1) {
      thumbnailInput1.value = "";
      thumbnailPreview1.src = "./assets/dist/img/gallery.jpg";
    }
  };

  window.removeImage2 = function () {
    if (thumbnailInput2 && thumbnailPreview2) {
      thumbnailInput2.value = "";
      thumbnailPreview2.src = "./assets/dist/img/gallery.jpg";
    }
  };
  // Image Preview all form
  if (thumbnailInput && thumbnailPreview) {
    thumbnailInput.addEventListener("change", function (event) {
      const reader = new FileReader();
      reader.onload = function () {
        thumbnailPreview.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    });
  }

  // Remove image function (bind via button onclick)
  window.removeImage = function () {
    if (thumbnailInput && thumbnailPreview) {
      thumbnailInput.value = "";
      thumbnailPreview.src = "./assets/dist/img/gallery.jpg";
    }
  };
});

// $(document).ready(function() {
//     $('#parent').select2({
//         placeholder: "-- Select --",
//         width: '100%'
//     });
// });

$(function () {
  $("#people").multiSelect();
  $("#ice-cream").multiSelect();
  $("#line-wrap-example").multiSelect({
    positionMenuWithin: $(".position-menu-within"),
  });
  $("#categories").multiSelect({
    noneText: "All categories",
    presets: [
      {
        name: "All categories",
        all: true,
      },
      {
        name: "My categories",
        options: ["a", "c"],
      },
    ],
  });
  $("#modal-example").multiSelect({
    modalHTML: '<div class="multi-select-modal"></div>',
  });

  // ✅ Add this line for the new #parent select
  $("#parent").multiSelect();
  $("#parent2").multiSelect();
  $(".right").removeClass("fa-angle-left").addClass("fa-angle-down");
  $('.menu-open').css('background-color', 'yellow');
  //=====check the current page url========
  const currentPage = window.location.pathname;
  if (currentPage.includes("master.php")) {
    $("#master_link").css("color", "white");
    $("#categories-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("general.php")) {
    $("#general_link").css("color", "white");
    $("#categories-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("specific.php")) {
    $("#specific_link").css("color", "white");
    $("#categories-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("import.php")) {
    $("#importsheet_link").css("color", "white");
    $("#tools-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("import_data.php")) {
    $("#importdata_link").css("color", "white");
    $("#tools-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("view_sheet.php")) {
    $("#viewsheet_link").css("color", "white");
    $("#view-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("all_projects.php")) {
    $("#projects-main").addClass("menu-open");
  } else if (currentPage.includes("add-newproject.php")) {
    $("#add_newproject_link").css("color", "white");
    $("#projects-main").addClass("menu-open");
  } else if (currentPage.includes("show-users.php")) {
    $("#users-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("add-user.php")) {
    $("#users-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("manage_roles.php")) {
    $("#users-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("manage_permissions.php")) {
    $("#users-main").addClass("menu-open");
    $("#alldata-main").addClass("menu-open");
  } else if (currentPage.includes("leads.php")) {
    $("#leads-main").addClass("menu-open");
    $("#crm-main").addClass("menu-open");
  } else if (currentPage.includes("contracts.php")) {
    $("#contracts-main").addClass("menu-open");
    $("#crm-main").addClass("menu-open");
  } else if (currentPage.includes("assignments.php")) {
    $("#assignments-main").addClass("menu-open");
    $("#crm-main").addClass("menu-open");
  } else if (currentPage.includes("tasks.php")) {
    $("#tasks-main").addClass("menu-open");
    $("#crm-main").addClass("menu-open");
  } else if (currentPage.includes("clients.php")) {
    $("#clients-main").addClass("menu-open");
    $("#crm-main").addClass("menu-open");
  } else if (currentPage.includes("portfolio.php")) {
    $("#portfolio-main").addClass("menu-open");
    $("#crm-main").addClass("menu-open");
  } else if (currentPage.includes("brands.php")) {
    $("#brands-main").addClass("menu-open");
    $("#crm-main").addClass("menu-open");
  } else {
    return 0;
  }

  //=========end current page url check====

});
