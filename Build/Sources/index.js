!(() => {
  let ready = (callback) => {
    if (document.readyState != "loading") callback();
    else document.addEventListener("DOMContentLoaded", callback);
  }

  // DOM ready
  ready(() => {

  });
})();
