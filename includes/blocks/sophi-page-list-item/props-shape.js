import PropTypes from 'prop-types';

export const propsShape = {
	attributes: PropTypes.shape({
		postUpdated: PropTypes.bool,
		postTitle: PropTypes.string,
		postExcept: PropTypes.string,
		postAuthor: PropTypes.string,
		postDate: PropTypes.string,
		featuredImage: PropTypes.string,
		linkToFeaturedImage: PropTypes.string,
		overrideRule: PropTypes.string,
		overrideData: PropTypes.object,
	}).isRequired,
	className: PropTypes.string,
};

export const editPropsShape = {
	...propsShape,
	clientId: PropTypes.string,
	setAttributes: PropTypes.func.isRequired,
};
