Object caching is encouraged, as the plugin saves Sophi data as a transient.  If you do not have object caching, then the data will be saved as a transient in the options table but note that these will eventually expire.

The default caching period is five minutes. This can be modified with the `sophi_cache_duration` hook.
