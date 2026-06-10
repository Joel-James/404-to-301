import { useState } from '@wordpress/element'
import { Spinner } from '@wordpress/components'

/**
 * Banner image with a native WP <Spinner /> shown while the asset
 * is in-flight. Banner assets come from Freemius and can be slow on
 * flaky connections, so we keep the card layout stable with a fixed
 * aspect-ratio container (see SCSS) and overlay the spinner until
 * the browser fires `load` (or `error`).
 *
 * The banner sits in a fixed ~3:1 card slot, so the relevant axis is
 * pixel density, not viewport width: `src` (the smaller
 * `card_banner_url`) covers standard displays, and `srcLarge` (the
 * larger `banner_url`) keeps the banner crisp on high-DPI / large
 * screens via a 1x/2x density `srcset`. When no distinct large asset
 * is available we fall back to a plain `src`.
 *
 * @param {Object} props
 * @param {string} props.src      Image URL (standard resolution).
 * @param {string} [props.srcLarge] High-resolution image URL.
 * @param {string} props.alt      Alternative text.
 */
const BannerImage = ({ src, srcLarge, alt }) => {
	const [isLoaded, setIsLoaded] = useState(false)

	const srcSet =
		srcLarge && srcLarge !== src ? `${src} 1x, ${srcLarge} 2x` : undefined

	return (
		<>
			{!isLoaded && (
				<span className="d404-addon-banner__spinner" aria-hidden="true">
					<Spinner />
				</span>
			)}
			<img
				src={src}
				srcSet={srcSet}
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
