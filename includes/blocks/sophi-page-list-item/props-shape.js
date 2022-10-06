import PropTypes from 'prop-types';

export const propsShape = {
	attributes: PropTypes.shape({
		postUpdated: PropTypes.bool,
		postTitle: PropTypes.string,
		postID: PropTypes.number,
		postLink: PropTypes.string,
		postExcept: PropTypes.string,
		postAuthor: PropTypes.string,
		postDate: PropTypes.string,
		postDateC: PropTypes.string,
		featuredImage: PropTypes.string,
		linkToFeaturedImage: PropTypes.bool,
		overrideRule: PropTypes.string,
		overrideLocation: PropTypes.string,
		overridePostID: PropTypes.number,
		overrideExpiry: PropTypes.number,
	}).isRequired,
	className: PropTypes.string,
};

export const editPropsShape = {
	...propsShape,
	clientId: PropTypes.string,
	setAttributes: PropTypes.func.isRequired,
};
