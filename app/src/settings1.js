require('./styles/settings.scss')

const DD4t3Settings = DD4t3Settings || {}
window.DD4t3Settings = DD4t3Settings

require( './scripts/settings/logs' )
require( './scripts/settings/common' )
require( './scripts/settings/general' )
require( './scripts/settings/redirects' )
require( './scripts/settings/notifications' )