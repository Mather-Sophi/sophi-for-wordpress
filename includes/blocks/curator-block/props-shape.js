import PropTypes from 'prop-types';

export const propsShape = {
	attributes: PropTypes.shape({
		customTitle: PropTypes.string,
		pageName: PropTypes.string,
		widgetName: PropTypes.string,
		displayPostExcept: PropTypes.bool,
		displayAuthor: PropTypes.bool,
		displayPostDate: PropTypes.bool,
		displayFeaturedImage: PropTypes.bool,
		addLinkToFeaturedImage: PropTypes.bool,
	}).isRequired,
	className: PropTypes.string,
};

export const editPropsShape = {
	...propsShape,
	clientId: PropTypes.string,
	isSelected: PropTypes.bool,
	setAttributes: PropTypes.func.isRequired,
};
