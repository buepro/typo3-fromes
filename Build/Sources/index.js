!(() => {
  let ready = (callback) => {
    if (document.readyState != "loading") callback();
    else document.addEventListener("DOMContentLoaded", callback);
  }

  ready(() => {
    let checkboxFilter = document.querySelector('#fm-filter-checkbox1');
    checkboxFilter.items = [
      { name: 'name1', value: 1, label: 'Label 1' },
      { name: 'name2', value: 2, label: 'Label 2' }
    ];
  });
})();
