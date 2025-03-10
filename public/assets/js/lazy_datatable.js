function delay (fn, ms) {
  let timer = 0;

  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(fn.bind(this, ...args), ms || 0);
  };
}

window.datatableParams = {
  serverSide: true,
  searching: false,
  scrollX: true,
  initComplete: function (settings) {
    const table = $(settings.nTable).DataTable();
    $('.form-filters').find('input, select')
      .off()
      .on('keyup change', delay(() =>
        table
          .search($(this).val().trim(), false, false)
          .draw()
      , 500)
      );
  }
};
