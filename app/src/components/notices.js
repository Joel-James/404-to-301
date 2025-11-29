import { NoticeList } from '@wordpress/components'
import { useDispatch, useSelect } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'

const Notices = () => {
	const { removeNotice } = useDispatch( noticesStore )
	const notices = useSelect( ( select ) =>
		select( noticesStore ).getNotices()
	)

	if ( notices.length === 0 ) {
		return null
	}

	return <NoticeList notices={ notices } onRemove={ removeNotice } />
}

export default Notices
