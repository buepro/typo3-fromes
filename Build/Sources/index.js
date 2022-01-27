!(() => {
  let ready = (callback) => {
    if (document.readyState != "loading") callback();
    else document.addEventListener("DOMContentLoaded", callback);
  }

  // DOM ready
  ready(() => {
    document.querySelector('fm-list button').addEventListener('click', function () {
      document.getElementById('fm-filter-result').items = [
        { id: 1, label: 'Name 1', email: 'name1@email.go' },
        { id: 5, label: 'Name 2', email: 'name2@email.go' },
        { id: 6, label: 'Name 3', email: 'name3@email.go' }
      ]
    })
    document.querySelector('fm-collector button').addEventListener('click', function () {
      document.getElementById('fm-collector').addItems(
        document.getElementById('fm-filter-result').selected
      );
    })
  });
})();
