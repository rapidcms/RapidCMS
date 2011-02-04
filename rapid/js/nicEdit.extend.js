/*global nicEditorInstance*/
nicEditorInstance.prototype.keyDown = function (e, t) {
	if (e.keyCode === 27) {
		this.ne.fireEvent('key', this, e);
	}
	if (e.ctrlKey) {
		this.ne.fireEvent('key', this, e);
	}
};