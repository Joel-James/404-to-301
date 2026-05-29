/**
 * Boot all plugin-side `@wordpress/data` stores.
 *
 * Each page entry imports this module once so the stores are
 * registered before any component renders. The store modules call
 * `register()` at import time — bringing them in here is enough.
 *
 * Importing this from multiple entries (one per React app) is safe:
 * `@wordpress/data`'s registry de-duplicates registrations under
 * the same store key.
 */
import './addons'
import './migration'
