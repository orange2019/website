var Category = {
	init: function () {
		this.categoryTypeChange();
	},
	categoryTypeChange: function () {

		var select = $('#category-type');
		// console.log(select)
		// var url = select.attr('data-id');
		select.bind('change', function () {
			// console.log(url)
			var type = select.val();
			// location.href = url + '?type=' + type;
		});

		var select1 = $('#posts-update');
		var url = select1.attr('data-url');
		select1.bind('change', function () {
			var category_id = select1.val();
			// console.log(url + '?category_id=' + category_id);
			location.href = url + '?category_id=' + category_id;
		});
	}
};

module.exports = Category;