// save estimation
document.addEventListener("saveMilestone", function(e) {
  $.post("save-milestone.php", e.detail, function(res) {
    if (res.success) {
      alert("Milestone saved");
      location.reload();
    }
  }, "json");
});
// delete estimation
document.addEventListener("removeMilestone", function(e) {
  if (!e.detail.id) return e.detail.block.remove();
  $.post("delete-milestone.php", { id: e.detail.id }, function(res) {
    if (res.success) {
      alert('milestone deleted!');
      e.detail.block.remove();
    }
  }, "json");
});
