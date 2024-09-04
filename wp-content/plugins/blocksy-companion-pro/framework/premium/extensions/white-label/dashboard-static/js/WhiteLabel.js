import {
	createElement,
	Component,
	useEffect,
	useState,
	Fragment,
} from '@wordpress/element'
import { __ } from 'ct-i18n'
import classnames from 'classnames'

import EditSettings from './EditSettings'

const WhiteLabel = ({ extension, onExtsSync }) => {
	if (extension.data.locked) {
		return null
	}

	return <EditSettings />
}

export default WhiteLabel
