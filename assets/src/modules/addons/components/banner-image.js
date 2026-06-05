import { useState } from '@wordpress/element'
import { Spinner } from '@wordpress/components'

/**
 * Banner image with a native WP <Spinner /> shown while the asset
 * is in-flight. Banner assets come from Freemius and can be slow on
 * flaky connections, so we keep the card layout stable with a fixed
 * aspect-ratio container (see SCSS) and overlay the spinner until
 * the browser fires `load` (or `error`).
 *
 * @param {Object} props
 * @param {string} props.src Image URL.
 * @param {string} props.alt Alternative text.
 */
const BannerImage = ({ src, alt }) => {
	const [isLoaded, setIsLoaded] = useState(false)

	return (
		<>
			{!isLoaded && (
				<span className="d404-addon-banner__spinner" aria-hidden="true">
					<Spinner />
				</span>
			)}
			<img
				src={src}
				alt={alt}
				loading="lazy"
				className="d404-addon-banner__img"
				onLoad={() => setIsLoaded(true)}
				onError={() => setIsLoaded(true)}
			/>
		</>
	)
}

export default BannerImage
