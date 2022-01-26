!(() => {
  let ready = (callback) => {
    if (document.readyState != "loading") callback();
    else document.addEventListener("DOMContentLoaded", callback);
  }

  // DOM ready
  ready(() => {
    document.querySelector('fm-list button').addEventListener('click', function () {
      document.getElementById('fm-filter-result').items = [
        { name: 'Name 1', email: 'name1@email.go' },
        { name: 'Name 2', email: 'name2@email.go' },
        { name: 'Name 3', email: 'name3@email.go' }
      ]
    })
  });
})();
