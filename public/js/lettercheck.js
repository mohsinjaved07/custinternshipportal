function check(checked = true) {
    const checkboxes = document.querySelectorAll('input[name="regno[]"]');
    checkboxes.forEach((checkbox) => {
        checkbox.checked = checked;
    });
  }
  
  function checkAll() {
    check();
    this.onclick = uncheckAll;
  }
  
  function uncheckAll() {
    check(false);
    this.onclick = checkAll;
  }
  
  const btn = document.querySelector('#checkbutton');
  btn.onclick = checkAll;
  