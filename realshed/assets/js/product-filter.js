// howMany = 12;
// listButton = $('button.list-view');
// gridButton = $('button.grid-view');
// wrapper = $('div.wrapper');

// listButton.on('click',function(){

//   gridButton.removeClass('on');
//   listButton.addClass('on');
//   wrapper.removeClass('grid').addClass('list');

// });

// gridButton.on('click',function(){

//   listButton.removeClass('on');
//   gridButton.addClass('on');
//   wrapper.removeClass('list').addClass('grid');

// });

howMany = 12;
listButton = $('button.list-view');
gridButton = $('button.grid-view');
wrapper = $('div.wrapper');

// Function to sync button active state with current wrapper class
function syncToggleButtons() {
  if (wrapper.hasClass('grid')) {
    gridButton.addClass('on');
    listButton.removeClass('on');
  } else {
    listButton.addClass('on');
    gridButton.removeClass('on');
  }
}

// Run on page load to set correct active button
syncToggleButtons();

// List view click handler
listButton.on('click', function () {
  gridButton.removeClass('on');
  listButton.addClass('on');
  wrapper.removeClass('grid').addClass('list');
});

// Grid view click handler
gridButton.on('click', function () {
  listButton.removeClass('on');
  gridButton.addClass('on');
  wrapper.removeClass('list').addClass('grid');
});
