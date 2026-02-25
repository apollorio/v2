
  ┌── apollo-statistics [WordPress] ──
  │   Errors: 124  Warnings: 77
  │
  ├─ ...\apollo-statistics\apollo-statistics.php
  │  ⚠ L44   Universal.NamingConventions.NoReservedKeywordParameterNames.classFound — It is recommended not to use reserved keyword "class" as function parameter name. Found: $class
  │  ✖ L72   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L89   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L106  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L136  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L168  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L171  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L175  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L179  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L183  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L187  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-statistics\includes\class-data-collector.php
  │  ✖ L20   Squiz.Commenting.ClassComment.Missing — Missing doc comment for class Data_Collector
  │  ✖ L22   Generic.Commenting.DocComment.MissingShort — Missing short description in doc comment
  │  ✖ L36   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L39   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L42   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L45   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L50   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L54   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L91   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_statistics_data_providers', but found: 'apollo/statistics/data_providers'.
  │  ⚠ L119  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L134  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_statistics_daily_collected', but found: 'apollo/statistics/daily_collected'.
  │  ✖ L147  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L151  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L151  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L166  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L167  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L167  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L194  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L197  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L198  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L198  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L213  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L215  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L215  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L215  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$rsvp_table} at "SHOW TABLES LIKE '{$rsvp_table}'"
  │  ✖ L219  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$rsvp_table} at                  FROM {$rsvp_table}\n
  │  ✖ L230  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L232  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$rsvp_table} at "SELECT event_id, COUNT(*) AS total FROM {$rsvp_table} WHERE status = 'going' GROUP BY event_id"
  │  ✖ L254  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L257  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L257  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L257  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$pages_table} at "SHOW TABLES LIKE '{$pages_table}'"
  │  ✖ L261  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L262  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L262  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L265  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$pages_table} at              FROM {$pages_table}\n
  │  ✖ L312  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L314  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_statistics_tracked_cpts', but found: 'apollo/statistics/tracked_cpts'.
  │  ✖ L322  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L330  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L331  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L335  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L351  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L366  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-statistics\includes\class-metrics-processor.php
  │  ✖ L19   Squiz.Commenting.ClassComment.Missing — Missing doc comment for class Metrics_Processor
  │  ✖ L32   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L35   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L51   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L70   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L71   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L71   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L84   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ⚠ L113  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L113  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L120  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ⚠ L149  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L149  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L157  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ✖ L197  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L198  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L198  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L200  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │  ✖ L207  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L208  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L208  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L210  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │  ✖ L218  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L259  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L259  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L262  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ✖ L272  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L320  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L330  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L352  Generic.Commenting.DocComment.MissingShort — Missing short description in doc comment
  │  ⚠ L353  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_statistics_retention_days', but found: 'apollo/statistics/retention_days'.
  │  ⚠ L363  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L363  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L365  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "DELETE FROM {$table} WHERE recorded_date < %s"
  │  ⚠ L389  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L389  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L391  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │  ⚠ L399  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L399  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L401  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │
  ├─ ...\apollo-statistics\includes\class-reports.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L16   Squiz.Commenting.ClassComment.Missing — Missing doc comment for class Reports
  │  ✖ L18   Generic.Commenting.DocComment.MissingShort — Missing short description in doc comment
  │  ✖ L32   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L35   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L38   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L41   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L67   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L75   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L116  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L186  Universal.Operators.StrictComparisons.LooseNotEqual — Loose comparisons are not allowed. Expected: "!=="; Found: "!="
  │  ✖ L186  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L188  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L242  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $days is never used
  │  ⚠ L289  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $days is never used
  │  ⚠ L335  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $days is never used
  │  ✖ L443  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L443  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L525  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-statistics\includes\class-rest-controller.php
  │  ✖ L23   Squiz.Commenting.ClassComment.Missing — Missing doc comment for class REST_Controller
  │  ✖ L25   Generic.Commenting.DocComment.MissingShort — Missing short description in doc comment
  │  ✖ L207  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │  ✖ L223  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │  ⚠ L233  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L233  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L241  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} e\n
  │  ✖ L255  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L264  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │  ⚠ L274  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L274  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L282  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} u\n
  │  ✖ L296  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L305  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │  ⚠ L327  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L327  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L331  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} c\n
  │  ✖ L333  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$where_sql} at              WHERE {$where_sql}\n
  │  ⚠ L336  WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare — Replacement variables found, but no valid placeholders found in the query.
  │  ✖ L345  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L358  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │  ✖ L377  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L393  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │  ⚠ L396  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L408  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │  ✖ L427  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │
  ├─ ...\apollo-statistics\includes\functions.php
  │  ✖ L27   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L29   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L31   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (event_id, metric_type, metric_value, recorded_date)\n
  │  ⚠ L41   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L41   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L41   WordPress.DB.PreparedSQL.NotPrepared — Use placeholders and $wpdb->prepare(); found $sql
  │  ✖ L57   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L60   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (user_id, metric_type, metric_value, recorded_date)\n
  │  ⚠ L70   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L70   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L70   WordPress.DB.PreparedSQL.NotPrepared — Use placeholders and $wpdb->prepare(); found $sql
  │  ✖ L86   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L87   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L90   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (post_id, post_type, metric_type, metric_value, recorded_date)\n
  │  ⚠ L101  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L101  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L101  WordPress.DB.PreparedSQL.NotPrepared — Use placeholders and $wpdb->prepare(); found $sql
  │  ✖ L115  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L122  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L123  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L123  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L125  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$content_table}\n
  │  ✖ L131  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L132  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L132  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L134  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$content_table}\n
  │  ✖ L140  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L141  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L141  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L143  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$users_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$users_table}\n
  │  ✖ L149  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L150  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L150  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L152  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$users_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$users_table}\n
  │  ✖ L158  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L159  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L159  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L168  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L169  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L169  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L172  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at          FROM {$content_table}\n
  │  ✖ L179  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L181  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L182  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L182  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L185  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at          FROM {$content_table} c\n
  │  ✖ L194  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L217  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L217  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L220  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} e\n
  │  ⚠ L232  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L232  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L235  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} u\n
  │  ⚠ L248  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L248  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L251  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} c\n
  │  ✖ L266  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L269  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L272  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  └────────────────────────────────────────────

  ┌── apollo-statistics [WordPress-Extra] ──
  │   Errors: 49  Warnings: 13
  │
  ├─ ...\apollo-statistics\apollo-statistics.php
  │  ⚠ L44   Universal.NamingConventions.NoReservedKeywordParameterNames.classFound — It is recommended not to use reserved keyword "class" as function parameter name. Found: $class
  │
  ├─ ...\apollo-statistics\includes\class-data-collector.php
  │  ⚠ L91   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_statistics_data_providers', but found: 'apollo/statistics/data_providers'.
  │  ⚠ L119  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L134  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_statistics_daily_collected', but found: 'apollo/statistics/daily_collected'.
  │  ✖ L215  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$rsvp_table} at "SHOW TABLES LIKE '{$rsvp_table}'"
  │  ✖ L219  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$rsvp_table} at                  FROM {$rsvp_table}\n
  │  ✖ L232  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$rsvp_table} at "SELECT event_id, COUNT(*) AS total FROM {$rsvp_table} WHERE status = 'going' GROUP BY event_id"
  │  ✖ L257  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$pages_table} at "SHOW TABLES LIKE '{$pages_table}'"
  │  ✖ L265  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$pages_table} at              FROM {$pages_table}\n
  │  ⚠ L314  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_statistics_tracked_cpts', but found: 'apollo/statistics/tracked_cpts'.
  │  ✖ L331  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L351  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L366  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-statistics\includes\class-metrics-processor.php
  │  ✖ L84   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ✖ L120  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ✖ L157  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ✖ L200  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │  ✖ L210  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │  ✖ L262  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ⚠ L353  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_statistics_retention_days', but found: 'apollo/statistics/retention_days'.
  │  ✖ L365  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "DELETE FROM {$table} WHERE recorded_date < %s"
  │  ✖ L391  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │  ✖ L401  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │
  ├─ ...\apollo-statistics\includes\class-reports.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L186  Universal.Operators.StrictComparisons.LooseNotEqual — Loose comparisons are not allowed. Expected: "!=="; Found: "!="
  │  ✖ L186  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L188  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L242  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $days is never used
  │  ⚠ L289  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $days is never used
  │  ⚠ L335  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $days is never used
  │  ✖ L443  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L443  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-statistics\includes\class-rest-controller.php
  │  ✖ L241  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} e\n
  │  ✖ L255  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L282  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} u\n
  │  ✖ L296  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L331  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} c\n
  │  ✖ L333  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$where_sql} at              WHERE {$where_sql}\n
  │  ⚠ L336  WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare — Replacement variables found, but no valid placeholders found in the query.
  │  ✖ L345  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L396  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │
  ├─ ...\apollo-statistics\includes\functions.php
  │  ✖ L27   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L31   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (event_id, metric_type, metric_value, recorded_date)\n
  │  ✖ L41   WordPress.DB.PreparedSQL.NotPrepared — Use placeholders and $wpdb->prepare(); found $sql
  │  ✖ L57   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L60   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (user_id, metric_type, metric_value, recorded_date)\n
  │  ✖ L70   WordPress.DB.PreparedSQL.NotPrepared — Use placeholders and $wpdb->prepare(); found $sql
  │  ✖ L86   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L87   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L90   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (post_id, post_type, metric_type, metric_value, recorded_date)\n
  │  ✖ L101  WordPress.DB.PreparedSQL.NotPrepared — Use placeholders and $wpdb->prepare(); found $sql
  │  ✖ L125  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$content_table}\n
  │  ✖ L134  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$content_table}\n
  │  ✖ L143  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$users_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$users_table}\n
  │  ✖ L152  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$users_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$users_table}\n
  │  ✖ L172  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at          FROM {$content_table}\n
  │  ✖ L179  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L185  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at          FROM {$content_table} c\n
  │  ✖ L194  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L220  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} e\n
  │  ✖ L235  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} u\n
  │  ✖ L251  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} c\n
  └────────────────────────────────────────────

  ┌── apollo-statistics [WordPress-VIP-Go] ──
  │   Errors: 39  Warnings: 64
  │
  ├─ ...\apollo-statistics\apollo-statistics.php
  │  ✖ L126  WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L138  WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │
  ├─ ...\apollo-statistics\includes\class-data-collector.php
  │  ✖ L119  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L151  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L151  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L167  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L167  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L198  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L198  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L215  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L215  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L215  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$rsvp_table} at "SHOW TABLES LIKE '{$rsvp_table}'"
  │  ✖ L219  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$rsvp_table} at                  FROM {$rsvp_table}\n
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L232  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$rsvp_table} at "SELECT event_id, COUNT(*) AS total FROM {$rsvp_table} WHERE status = 'going' GROUP BY event_id"
  │  ⚠ L257  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L257  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L257  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$pages_table} at "SHOW TABLES LIKE '{$pages_table}'"
  │  ⚠ L262  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L262  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L265  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$pages_table} at              FROM {$pages_table}\n
  │  ✖ L337  WordPressVIPMinimum.Functions.RestrictedFunctions.cookies_setcookie — Due to server-side caching, server-side based client related logic might not work. We recommend implementing client side logic in JavaScript instead.
  │
  ├─ ...\apollo-statistics\includes\class-metrics-processor.php
  │  ⚠ L71   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L71   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L84   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ⚠ L113  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L113  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L120  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ⚠ L149  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L149  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L157  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ⚠ L198  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L198  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L200  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │  ⚠ L208  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L208  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L210  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │  ⚠ L259  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L259  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L262  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ⚠ L363  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L363  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L365  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "DELETE FROM {$table} WHERE recorded_date < %s"
  │  ⚠ L389  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L389  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L391  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │  ⚠ L399  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L399  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L401  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$table}\n
  │
  ├─ ...\apollo-statistics\includes\class-rest-controller.php
  │  ⚠ L233  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L233  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L241  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} e\n
  │  ⚠ L274  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L274  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L282  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} u\n
  │  ✖ L283  WordPressVIPMinimum.Variables.RestrictedVariables.user_meta__wpdb__users — Usage of users tables is highly discouraged in VIP context
  │  ⚠ L327  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L327  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L331  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} c\n
  │  ✖ L333  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$where_sql} at              WHERE {$where_sql}\n
  │
  ├─ ...\apollo-statistics\includes\functions.php
  │  ✖ L31   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (event_id, metric_type, metric_value, recorded_date)\n
  │  ⚠ L41   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L41   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L41   WordPress.DB.PreparedSQL.NotPrepared — Use placeholders and $wpdb->prepare(); found $sql
  │  ✖ L60   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (user_id, metric_type, metric_value, recorded_date)\n
  │  ⚠ L70   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L70   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L70   WordPress.DB.PreparedSQL.NotPrepared — Use placeholders and $wpdb->prepare(); found $sql
  │  ✖ L90   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (post_id, post_type, metric_type, metric_value, recorded_date)\n
  │  ⚠ L101  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L101  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L101  WordPress.DB.PreparedSQL.NotPrepared — Use placeholders and $wpdb->prepare(); found $sql
  │  ⚠ L123  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L123  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L125  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$content_table}\n
  │  ⚠ L132  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L132  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L134  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$content_table}\n
  │  ⚠ L141  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L141  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L143  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$users_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$users_table}\n
  │  ⚠ L150  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L150  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L152  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$users_table} at "SELECT COALESCE(SUM(metric_value), 0) FROM {$users_table}\n
  │  ⚠ L159  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L159  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L169  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L169  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L172  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at          FROM {$content_table}\n
  │  ⚠ L182  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L182  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L185  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$content_table} at          FROM {$content_table} c\n
  │  ⚠ L217  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L217  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L220  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} e\n
  │  ⚠ L232  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L232  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L235  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} u\n
  │  ✖ L236  WordPressVIPMinimum.Variables.RestrictedVariables.user_meta__wpdb__users — Usage of users tables is highly discouraged in VIP context
  │  ⚠ L248  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L248  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L251  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} c\n
  └────────────────────────────────────────────

  ┌── apollo-templates [WordPress] ──
  │   Errors: 695  Warnings: 154
  │
  ├─ ...\apollo-templates\apollo-templates.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L49   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L61   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L79   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L84   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L86   Universal.NamingConventions.NoReservedKeywordParameterNames.classFound — It is recommended not to use reserved keyword "class" as function parameter name. Found: $class
  │  ✖ L150  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L155  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L163  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L171  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L192  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L198  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L214  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$templates" missing
  │  ✖ L231  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$template" missing
  │  ✖ L237  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L269  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$template" missing
  │  ✖ L275  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L285  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L297  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$template" missing
  │  ✖ L330  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L331  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['apollo_login_nonce'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L331  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['apollo_login_nonce']
  │  ✖ L335  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['user'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L336  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['pass'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L336  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['pass']
  │  ✖ L337  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L373  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['pwd'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L373  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['pwd']
  │  ✖ L417  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L428  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L431  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L460  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_events_suggestion_created', but found: 'apollo/events/suggestion_created'.
  │  ✖ L475  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L480  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L484  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L488  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L501  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L506  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L511  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\create-page.php
  │  ✖ L1    Squiz.Commenting.FileComment.Missing — Missing file doc comment
  │  ✖ L10   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L11   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $page
  │  ✖ L29   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"✅ Página 'Classificados' criada com ID: $page_id<br>"'.
  │  ✖ L33   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$page_id'.
  │  ✖ L37   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"ℹ️ Página 'Classificados' já existe (ID: {$page->ID})<br>"'.
  │  ✖ L39   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L44   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L48   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L48   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-templates\examples\user-radar-examples.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-apollo-top-users-widget.php, but found user-radar-examples.php.
  │  ✖ L19   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L29   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L32   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L34   Squiz.PHP.CommentedOutCode.Found — This comment is 57% valid code; is this commented out code?
  │  ✖ L34   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L36   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L41   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L43   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"User is ranked #{$ranking}"'.
  │  ✖ L49   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L51   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Updated {$updated} user rankings"'.
  │  ✖ L53   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L56   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"{$user->display_name} - Ranking: "'.
  │  ✖ L56   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'UserRadar'.
  │  ✖ L59   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L63   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L66   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L68   WordPress.PHP.DevelopmentFunctions.error_log_print_r — print_r() found. Debug code should not normally be used in production.
  │  ⚠ L69   Squiz.PHP.CommentedOutCode.Found — This comment is 49% valid code; is this commented out code?
  │  ✖ L69   Squiz.Commenting.BlockComment.NoEmptyLineBefore — Empty line required before block comment
  │  ✖ L83   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L87   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L90   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L119  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$actions" missing
  │  ✖ L119  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$user_object" missing
  │  ✖ L168  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_GET['action'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L170  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L173  WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ✖ L177  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L180  WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ✖ L222  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │  ⚠ L225  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L236  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$request" missing
  │  ✖ L269  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$atts" missing
  │  ✖ L298  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$atts" missing
  │  ✖ L327  Universal.Files.SeparateFunctionsFromOO.Mixed — A file should either contain function declarations or OO structure declarations, but not both. Found 12 function declaration(s) and 1 OO structure declaration(s). The first function declaration was found on line 100; the first OO declaration was found on line 327
  │  ✖ L329  Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function __construct()
  │  ✖ L337  Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function widget()
  │  ✖ L341  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['before_widget']'.
  │  ✖ L342  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['before_title']'.
  │  ✖ L342  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['after_title']'.
  │  ✖ L352  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ranking'.
  │  ✖ L358  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['after_widget']'.
  │  ✖ L361  Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function form()
  │  ✖ L365  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$this'.
  │  ✖ L370  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$this'.
  │  ✖ L371  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$this'.
  │  ✖ L380  Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function update()
  │  ✖ L387  Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function apollo_register_top_users_widget()
  │  ✖ L396  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$user_id" missing
  │  ✖ L396  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$badges" missing
  │  ✖ L404  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L421  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$user_id" missing
  │  ✖ L421  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$new_position" missing
  │  ✖ L428  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\includes\class-navbar-settings.php
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-navbarsettings.php, but found class-navbar-settings.php.
  │  ✖ L33   Squiz.Commenting.VariableComment.MissingVar — Missing @var tag in member variable comment
  │  ✖ L36   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function get_instance()
  │  ✖ L43   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function __construct()
  │  ✖ L84   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$hook" missing
  │  ✖ L92   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L153  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$input" missing
  │  ✖ L172  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L173  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L274  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L278  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L287  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L288  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L368  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$index" missing
  │  ✖ L368  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$app" missing
  │
  ├─ ...\apollo-templates\includes\class-persistent-ui.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-persistentui.php, but found class-persistent-ui.php.
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L32   Squiz.Commenting.ClassComment.Missing — Missing doc comment for class PersistentUI
  │  ✖ L176  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ⚠ L199  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_seo_head', but found: 'apollo/seo/head'.
  │  ✖ L201  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L204  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L208  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ⚠ L216  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_canvas_head', but found: 'apollo/canvas/head'.
  │
  ├─ ...\apollo-templates\includes\class-plugin.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L47   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L56   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L59   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L65   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L69   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L116  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L124  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L148  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L159  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L170  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L181  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L229  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L277  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\includes\class-shortcodes.php
  │  ✖ L65   Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L119  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L165  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L197  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L229  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L247  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L274  WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ⚠ L275  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L305  WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │
  ├─ ...\apollo-templates\includes\constants.php
  │  ✖ L17   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\includes\functions.php
  │  ✖ L27   Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L89   Generic.Commenting.DocComment.MissingShort — Missing short description in doc comment
  │  ✖ L94   WordPress.PHP.DontExtract.extract_extract — extract() usage is highly discouraged, due to the complexity and unintended issues it might cause.
  │  ✖ L99   Generic.Commenting.DocComment.MissingShort — Missing short description in doc comment
  │  ✖ L120  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L155  WordPress.PHP.DontExtract.extract_extract — extract() usage is highly discouraged, due to the complexity and unintended issues it might cause.
  │
  ├─ ...\apollo-templates\includes\pages.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L21   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L39   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L66   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L77   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$wp" missing
  │  ⚠ L81   WordPress.WP.AlternativeFunctions.parse_url_parse_url — parse_url() is discouraged because of inconsistency in the output across PHP versions; use wp_parse_url() instead.
  │  ✖ L81   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_SERVER['REQUEST_URI'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L81   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L83   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L87   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L91   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L97   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$template" missing
  │  ✖ L124  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L132  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L136  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L137  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L159  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['state'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L159  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['state']
  │  ✖ L165  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L196  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\includes\weather-helpers.php
  │  ✖ L20   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L30   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L32   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L36   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L52   WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L60   WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L66   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L75   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L80   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L91   Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L96   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L102  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L116  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L126  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L140  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L166  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L184  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L215  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$default" missing
  │  ⚠ L218  Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound — It is recommended not to use reserved keyword "default" as function parameter name. Found: $default
  │  ✖ L223  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$default" missing
  │  ⚠ L226  Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound — It is recommended not to use reserved keyword "default" as function parameter name. Found: $default
  │  ✖ L231  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$default" missing
  │  ⚠ L234  Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound — It is recommended not to use reserved keyword "default" as function parameter name. Found: $default
  │
  ├─ ...\apollo-templates\src\Activation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected activation.php, but found Activation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-activation.php, but found Activation.php.
  │  ✖ L35   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L38   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L41   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L44   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L47   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L50   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L61   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L71   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L116  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L119  WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents — File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: file_put_contents().
  │
  ├─ ...\apollo-templates\src\Deactivation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected deactivation.php, but found Deactivation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-deactivation.php, but found Deactivation.php.
  │  ✖ L35   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L38   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L41   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\src\FrontendEditor.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected frontendeditor.php, but found FrontendEditor.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-frontendeditor.php, but found FrontendEditor.php.
  │  ✖ L78   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L83   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L86   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L93   Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L163  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L199  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L214  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L230  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L247  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L286  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L306  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L311  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L314  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L317  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L358  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $config is never used
  │  ✖ L389  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L392  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L398  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L420  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L421  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L426  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L441  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L442  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L446  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L449  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L485  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$current_len'.
  │  ✖ L485  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$maxlength'.
  │  ✖ L494  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L507  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L521  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L527  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L535  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L550  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L551  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L568  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L569  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L570  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L584  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L597  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L602  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L604  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L608  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L613  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L635  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L643  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L648  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['post_id'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L648  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['post_id']
  │  ✖ L655  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L665  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L668  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L681  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L681  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L685  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L689  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L692  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L693  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L693  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L702  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L715  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L723  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L734  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L742  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L747  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L776  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L781  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['post_id'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L781  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['post_id']
  │  ✖ L806  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L807  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L810  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L837  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['post_id'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L837  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['post_id']
  │  ✖ L848  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L864  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L876  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L907  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L925  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L934  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L943  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\src\FrontendFields.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected frontendfields.php, but found FrontendFields.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-frontendfields.php, but found FrontendFields.php.
  │  ✖ L48   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function __construct()
  │  ✖ L53   Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L70   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L76   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L85   Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L94   Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $value is never used
  │  ✖ L127  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L137  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L150  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L160  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L173  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L183  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L195  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L205  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L217  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L227  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L239  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L249  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L259  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L263  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L267  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L276  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L286  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L301  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L311  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L335  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L345  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L351  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L362  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L382  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L396  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L398  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L445  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L455  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L458  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L481  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L496  Squiz.PHP.CommentedOutCode.Found — This comment is 58% valid code; is this commented out code?
  │  ✖ L496  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L518  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L528  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $value is never used
  │  ⚠ L528  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L546  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ⚠ L556  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $value is never used
  │  ⚠ L556  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │
  ├─ ...\apollo-templates\src\FrontendRouter.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected frontendrouter.php, but found FrontendRouter.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-frontendrouter.php, but found FrontendRouter.php.
  │  ✖ L51   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function __construct()
  │  ✖ L67   Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L77   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L136  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L151  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L159  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L160  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L171  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L176  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L187  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L196  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L208  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L214  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L239  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L257  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L276  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L279  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L282  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L295  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L308  WordPress.PHP.DontExtract.extract_extract — extract() usage is highly discouraged, due to the complexity and unintended issues it might cause.
  │
  ├─ ...\apollo-templates\src\Plugin.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected plugin.php, but found Plugin.php.
  │  ✖ L20   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\auth\login-register.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L72   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L105  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L108  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L111  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L137  WordPress.WP.I18n.NoEmptyStrings — The $text text string should have translatable content. Found: ''
  │  ✖ L165  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ⚠ L168  Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │  ✖ L168  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_aptitude-quiz.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-aptitude-quiz.php, but found new_aptitude-quiz.php.
  │  ✖ L24   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_footer.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-footer.php, but found new_footer.php.
  │  ✖ L18   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_header.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-header.php, but found new_header.php.
  │  ✖ L19   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L24   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L29   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L45   WordPress.WP.I18n.NoEmptyStrings — The $text text string should have translatable content. Found: ''
  │  ✖ L46   WordPress.WP.I18n.NoEmptyStrings — The $text text string should have translatable content. Found: ''
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_lockout-overlay.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-lockout-overlay.php, but found new_lockout-overlay.php.
  │  ✖ L18   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_login-form.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-login-form.php, but found new_login-form.php.
  │  ✖ L48   WordPress.WP.I18n.NoEmptyStrings — The $text text string should have translatable content. Found: ''
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_register-form.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-register-form.php, but found new_register-form.php.
  │
  ├─ ...\apollo-templates\templates\edit-post.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L35   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L40   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L45   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L57   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L61   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L65   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L80   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L87   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L109  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L120  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L125  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L154  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L184  Squiz.Commenting.BlockComment.CloserSameLine — Comment closer must be on a new line
  │  ✖ L227  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L228  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L229  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L240  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$card_index'.
  │  ✖ L244  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L249  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L252  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L298  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L299  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L308  WordPress.PHP.StrictInArray.MissingTrueStrict — Not using strict comparison for in_array; supply true for $strict argument.
  │  ✖ L309  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'mb_strlen'.
  │  ✖ L314  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L317  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L333  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$card_index'.
  │  ✖ L343  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L346  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L363  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L371  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L403  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L404  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L413  WordPress.PHP.StrictInArray.MissingTrueStrict — Not using strict comparison for in_array; supply true for $strict argument.
  │  ✖ L414  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'mb_strlen'.
  │  ✖ L419  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L422  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L500  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L503  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ⚠ L517  Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │  ✖ L517  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\event\card-style-01.php
  │  ✖ L27   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $title
  │  ✖ L39   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $year
  │  ✖ L94   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L103  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L115  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L126  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L161  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L209  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$time_display'.
  │
  ├─ ...\apollo-templates\templates\page-classificados.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L19   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L23   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L24   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L24   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L24   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_GET['intent'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ⚠ L25   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L25   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L25   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_GET['domain'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L26   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $search
  │  ⚠ L26   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L26   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L26   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_GET['s'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L27   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $paged
  │  ⚠ L27   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L27   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L29   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L39   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L56   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ✖ L59   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L66   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L87   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L91   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L94   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L94   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'APOLLO_TEMPLATES_URL'.
  │  ✖ L100  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L120  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L125  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L135  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L140  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L159  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L161  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$term'.
  │  ✖ L173  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$term'.
  │  ✖ L188  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L198  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$classifieds'.
  │  ✖ L200  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L220  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L223  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L227  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L233  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L259  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L268  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L272  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$wow_count'.
  │  ✖ L301  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L305  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'human_time_diff'.
  │  ⚠ L305  WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L315  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$author_id'.
  │  ✖ L316  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L321  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L339  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$comments_total'.
  │  ✖ L339  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L363  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$paged'.
  │  ✖ L363  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$classifieds'.
  │  ✖ L380  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L391  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │  ✖ L396  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L500  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ⚠ L518  Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │  ✖ L518  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\page-home.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L26   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_home_before_content', but found: 'apollo/home/before_content'.
  │  ✖ L42   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L45   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L48   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ⚠ L232  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_home_head', but found: 'apollo/home/head'.
  │  ✖ L296  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ⚠ L353  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_home_after_content', but found: 'apollo/home/after_content'.
  │
  ├─ ...\apollo-templates\templates\page-mapa.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ⚠ L39   WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ⚠ L42   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L78   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L85   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L151  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L473  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L474  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ⚠ L819  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_mapa_head', but found: 'apollo/mapa/head'.
  │  ✖ L891  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ⚠ L1186 WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_mapa_after_content', but found: 'apollo/mapa/after_content'.
  │
  ├─ ...\apollo-templates\templates\page-mural.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ⚠ L18   WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ✖ L22   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L27   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L31   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L38   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ⚠ L61   WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ⚠ L63   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L84   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L92   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ⚠ L104  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L104  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L105  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L105  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L109  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ⚠ L116  WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ⚠ L119  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L142  WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ⚠ L157  WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ⚠ L174  WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L217  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L220  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L224  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L225  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L233  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L306  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │
  ├─ ...\apollo-templates\templates\page-sobre.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L34   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L39   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L40   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L41   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L44   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L48   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L49   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L178  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\page-test.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L17   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L1604 Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L1616 WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L2137 Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L2151 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $comment
  │  ✖ L2154 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L2156 Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L2158 WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L2161 WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L2170 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$row_index'.
  │  ✖ L2172 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$display_url'.
  │  ✖ L2327 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │
  ├─ ...\apollo-templates\templates\template-parts\home\classifieds.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L21   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ⚠ L28   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L44   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ⚠ L51   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L78   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L80   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L81   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L129  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-templates\templates\template-parts\home\events-listing.php
  │  ✖ L14   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $per_page
  │  ✖ L15   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $orderby
  │  ✖ L16   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $order
  │  ⚠ L24   WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ⚠ L27   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L71   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L76   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L103  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L126  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L137  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-templates\templates\template-parts\home\footer.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L65   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $link
  │  ✖ L67   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$link['attrs']'.
  │  ✖ L98   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\template-parts\home\hero.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │
  ├─ ...\apollo-templates\templates\template-parts\home\hub-section.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L32   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L33   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-templates\templates\template-parts\home\infra.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\home\marquee.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\home\mission.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │
  ├─ ...\apollo-templates\templates\template-parts\home\tools-accordion.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\classifieds.php
  │  ✖ L14   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L33   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L60   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\favorites.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L15   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L20   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\greeting.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L16   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L22   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L23   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L24   Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │  ✖ L27   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L29   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L40   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L44   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L45   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L54   Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L56   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L57   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L67   Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L69   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L70   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L78   Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L80   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L81   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L81   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L98   Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L100  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L101  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L110  Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L112  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L113  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L113  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L125  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L162  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L163  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L165  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\news.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L15   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L23   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L27   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L50   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post
  │  ✖ L58   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L59   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\same-vibe.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L14   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L19   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\sounds.php
  │  ✖ L14   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L26   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ⚠ L27   WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode — urlencode() should only be used when dealing with legacy applications rawurlencode() should now be used instead. See https://www.php.net/function.rawurlencode and http://www.faqs.org/rfcs/rfc3986.html
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\ticker.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L13   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L545  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\upcoming.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L12   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L17   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L33   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L105  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L109  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L113  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L117  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L136  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\weather-hero.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L17   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\template-parts\navbar-old-backup.php
  │  ✖ L15   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L16   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L19   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L20   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L24   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\template-parts\navbar.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L18   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L24   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $names
  │  ✖ L32   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L37   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$bg_style'.
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon_style'.
  │  ✖ L322  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │  ✖ L328  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L338  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L342  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L350  WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ⚠ L353  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L369  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L371  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │
  ├─ ...\apollo-templates\templates\template-parts\navbar.v1.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected navbar-v1.php, but found navbar.v1.php.
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L18   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L24   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $names
  │  ✖ L32   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L37   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$bg_style'.
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon_style'.
  │  ✖ L322  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │  ✖ L328  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L338  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L342  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L350  WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ⚠ L353  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L369  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L371  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\classifieds.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L57   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L60   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L63   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\crash.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ⚠ L29   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ✖ L40   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L75   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L79   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L83   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L85   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L92   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L93   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L123  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\events.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ⚠ L24   WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ⚠ L27   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L38   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L72   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L116  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $status
  │  ✖ L121  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L123  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L126  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L128  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L130  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L130  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ⚠ L130  WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L133  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L137  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L141  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L160  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L172  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L176  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L193  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\footer.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L45   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\hero.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\map.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L18   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L26   WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ⚠ L29   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L50   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L61   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L62   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L63   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L77   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L167  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\marquee.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ⚠ L23   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_home_marquee_items', but found: 'apollo/home/marquee_items'.
  │  ✖ L55   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\menu-fab.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\navbar.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L26   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-acesso.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L18   Squiz.Commenting.FileComment.SpacingAfterComment — There must be exactly one blank line after the file comment
  │  ⚠ L54   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_login_render_panel', but found: 'apollo/login/render_panel'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-chat-inbox.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L14   Squiz.Commenting.FileComment.SpacingAfterComment — There must be exactly one blank line after the file comment
  │  ⚠ L58   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_chat_render_thread', but found: 'apollo/chat/render_thread'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-chat-list.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L14   Squiz.Commenting.FileComment.SpacingAfterComment — There must be exactly one blank line after the file comment
  │  ⚠ L60   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_chat_render_list', but found: 'apollo/chat/render_list'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-chat.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ⚠ L28   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_chat_render_panel', but found: 'apollo/chat/render_panel'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-detail.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ⚠ L78   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_detail_render_panel', but found: 'apollo/detail/render_panel'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-dynamic.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L19   Squiz.Commenting.FileComment.SpacingAfterComment — There must be exactly one blank line after the file comment
  │  ⚠ L77   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_dynamic_after_content', but found: 'apollo/dynamic/after_content'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-event-page.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L18   Squiz.Commenting.FileComment.SpacingAfterComment — There must be exactly one blank line after the file comment
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-explore.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L17   Squiz.Commenting.FileComment.SpacingAfterComment — There must be exactly one blank line after the file comment
  │  ⚠ L58   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_social_render_feed', but found: 'apollo/social/render_feed'.
  │  ⚠ L74   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_explore_after_feed', but found: 'apollo/explore/after_feed'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-forms.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L14   Squiz.Commenting.FileComment.SpacingAfterComment — There must be exactly one blank line after the file comment
  │  ⚠ L96   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_forms_extra_nav_items', but found: 'apollo/forms/extra_nav_items'.
  │  ⚠ L106  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_events_render_create_form', but found: 'apollo/events/render_create_form'.
  │  ⚠ L113  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_adverts_render_create_form', but found: 'apollo/adverts/render_create_form'.
  │  ⚠ L120  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_groups_render_create_form', but found: 'apollo/groups/render_create_form'.
  │  ⚠ L127  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_mod_render_report_form', but found: 'apollo/mod/render_report_form'.
  │  ⚠ L134  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_comment_render_depoimento_form', but found: 'apollo/comment/render_depoimento_form'.
  │  ⚠ L137  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_forms_extra_sections', but found: 'apollo/forms/extra_sections'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-mural.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L19   Squiz.Commenting.FileComment.SpacingAfterComment — There must be exactly one blank line after the file comment
  │  ⚠ L56   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_mural_after_content', but found: 'apollo/mural/after_content'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-notif.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ⚠ L28   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_notif_render_panel', but found: 'apollo/notif/render_panel'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\radio.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\tracks.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L49   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L53   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L60   WordPress.WP.I18n.MissingTranslatorsComment — A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders.
  │  ✖ L71   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L74   WordPress.WP.AlternativeFunctions.rand_rand — rand() is discouraged. Use the far less predictable wp_rand() instead.
  │
  ├─ ...\apollo-templates\test-timezone.php
  │  ✖ L5    Squiz.Commenting.FileComment.MissingPackageTag — Missing @package tag in file comment
  │  ✖ L7    Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L13   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'date'.
  │  ✖ L13   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L14   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'date_default_timezone_get'.
  │  ✖ L16   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ⚠ L17   WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L17   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L18   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L19   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L20   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L21   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L23   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_option'.
  │  ✖ L24   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_option'.
  │  ✖ L27   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$hour'.
  │  ✖ L39   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$greeting'.
  │
  ├─ ...\apollo-templates\_debug_test.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected -debug-test.php, but found _debug_test.php.
  │  ✖ L1    Squiz.Commenting.FileComment.Missing — Missing file doc comment
  │  ✖ L5    Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L13   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"$p => $q\n"'.
  │  ✖ L18   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Found test rules: $found\n"'.
  │  ✖ L20   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L24   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L34   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"AFTER: $p => $q\n"'.
  │  ✖ L39   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Found after re-flush: $found2\n"'.
  └────────────────────────────────────────────

  ┌── apollo-templates [WordPress-Extra] ──
  │   Errors: 267  Warnings: 124
  │
  ├─ ...\apollo-templates\apollo-templates.php
  │  ⚠ L86   Universal.NamingConventions.NoReservedKeywordParameterNames.classFound — It is recommended not to use reserved keyword "class" as function parameter name. Found: $class
  │  ✖ L337  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L460  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_events_suggestion_created', but found: 'apollo/events/suggestion_created'.
  │
  ├─ ...\apollo-templates\create-page.php
  │  ✖ L11   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $page
  │  ✖ L29   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"✅ Página 'Classificados' criada com ID: $page_id<br>"'.
  │  ✖ L33   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$page_id'.
  │  ✖ L37   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"ℹ️ Página 'Classificados' já existe (ID: {$page->ID})<br>"'.
  │  ✖ L48   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L48   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-templates\examples\user-radar-examples.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-apollo-top-users-widget.php, but found user-radar-examples.php.
  │  ⚠ L34   Squiz.PHP.CommentedOutCode.Found — This comment is 57% valid code; is this commented out code?
  │  ✖ L43   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"User is ranked #{$ranking}"'.
  │  ✖ L51   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Updated {$updated} user rankings"'.
  │  ✖ L56   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"{$user->display_name} - Ranking: "'.
  │  ✖ L56   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'UserRadar'.
  │  ⚠ L68   WordPress.PHP.DevelopmentFunctions.error_log_print_r — print_r() found. Debug code should not normally be used in production.
  │  ⚠ L69   Squiz.PHP.CommentedOutCode.Found — This comment is 49% valid code; is this commented out code?
  │  ✖ L170  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L173  WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ✖ L177  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L180  WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ⚠ L225  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L327  Universal.Files.SeparateFunctionsFromOO.Mixed — A file should either contain function declarations or OO structure declarations, but not both. Found 12 function declaration(s) and 1 OO structure declaration(s). The first function declaration was found on line 100; the first OO declaration was found on line 327
  │  ✖ L341  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['before_widget']'.
  │  ✖ L342  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['before_title']'.
  │  ✖ L342  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['after_title']'.
  │  ✖ L352  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ranking'.
  │  ✖ L358  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['after_widget']'.
  │  ✖ L365  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$this'.
  │  ✖ L370  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$this'.
  │  ✖ L371  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$this'.
  │
  ├─ ...\apollo-templates\includes\class-navbar-settings.php
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-navbarsettings.php, but found class-navbar-settings.php.
  │  ✖ L172  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L173  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L288  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-templates\includes\class-persistent-ui.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-persistentui.php, but found class-persistent-ui.php.
  │  ⚠ L199  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_seo_head', but found: 'apollo/seo/head'.
  │  ✖ L201  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L204  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L208  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ⚠ L216  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_canvas_head', but found: 'apollo/canvas/head'.
  │
  ├─ ...\apollo-templates\includes\class-plugin.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\includes\class-shortcodes.php
  │  ✖ L247  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-templates\includes\functions.php
  │  ✖ L94   WordPress.PHP.DontExtract.extract_extract — extract() usage is highly discouraged, due to the complexity and unintended issues it might cause.
  │  ✖ L155  WordPress.PHP.DontExtract.extract_extract — extract() usage is highly discouraged, due to the complexity and unintended issues it might cause.
  │
  ├─ ...\apollo-templates\includes\pages.php
  │  ⚠ L81   WordPress.WP.AlternativeFunctions.parse_url_parse_url — parse_url() is discouraged because of inconsistency in the output across PHP versions; use wp_parse_url() instead.
  │  ✖ L83   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L87   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L91   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L137  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-templates\includes\weather-helpers.php
  │  ✖ L32   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L52   WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L60   WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L218  Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound — It is recommended not to use reserved keyword "default" as function parameter name. Found: $default
  │  ⚠ L226  Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound — It is recommended not to use reserved keyword "default" as function parameter name. Found: $default
  │  ⚠ L234  Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound — It is recommended not to use reserved keyword "default" as function parameter name. Found: $default
  │
  ├─ ...\apollo-templates\src\Activation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected activation.php, but found Activation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-activation.php, but found Activation.php.
  │  ⚠ L119  WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents — File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: file_put_contents().
  │
  ├─ ...\apollo-templates\src\Deactivation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected deactivation.php, but found Deactivation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-deactivation.php, but found Deactivation.php.
  │
  ├─ ...\apollo-templates\src\FrontendEditor.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected frontendeditor.php, but found FrontendEditor.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-frontendeditor.php, but found FrontendEditor.php.
  │  ⚠ L358  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $config is never used
  │  ✖ L421  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L442  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L485  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$current_len'.
  │  ✖ L485  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$maxlength'.
  │  ✖ L604  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L613  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L681  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L681  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L693  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L693  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L715  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L807  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L848  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-templates\src\FrontendFields.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected frontendfields.php, but found FrontendFields.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-frontendfields.php, but found FrontendFields.php.
  │  ⚠ L94   Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $value is never used
  │  ⚠ L137  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ⚠ L160  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ⚠ L183  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ⚠ L205  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ⚠ L227  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ⚠ L249  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L259  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L263  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L267  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L286  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ⚠ L311  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ⚠ L345  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L398  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L455  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ✖ L481  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L496  Squiz.PHP.CommentedOutCode.Found — This comment is 58% valid code; is this commented out code?
  │  ⚠ L528  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $value is never used
  │  ⚠ L528  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │  ⚠ L556  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $value is never used
  │  ⚠ L556  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $post is never used
  │
  ├─ ...\apollo-templates\src\FrontendRouter.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected frontendrouter.php, but found FrontendRouter.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-frontendrouter.php, but found FrontendRouter.php.
  │  ✖ L160  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L308  WordPress.PHP.DontExtract.extract_extract — extract() usage is highly discouraged, due to the complexity and unintended issues it might cause.
  │
  ├─ ...\apollo-templates\src\Plugin.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected plugin.php, but found Plugin.php.
  │
  ├─ ...\apollo-templates\templates\auth\login-register.php
  │  ✖ L105  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L108  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L111  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L137  WordPress.WP.I18n.NoEmptyStrings — The $text text string should have translatable content. Found: ''
  │  ✖ L165  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ⚠ L168  Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_aptitude-quiz.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-aptitude-quiz.php, but found new_aptitude-quiz.php.
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_footer.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-footer.php, but found new_footer.php.
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_header.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-header.php, but found new_header.php.
  │  ✖ L45   WordPress.WP.I18n.NoEmptyStrings — The $text text string should have translatable content. Found: ''
  │  ✖ L46   WordPress.WP.I18n.NoEmptyStrings — The $text text string should have translatable content. Found: ''
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_lockout-overlay.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-lockout-overlay.php, but found new_lockout-overlay.php.
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_login-form.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-login-form.php, but found new_login-form.php.
  │  ✖ L48   WordPress.WP.I18n.NoEmptyStrings — The $text text string should have translatable content. Found: ''
  │
  ├─ ...\apollo-templates\templates\auth\parts\new_register-form.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected new-register-form.php, but found new_register-form.php.
  │
  ├─ ...\apollo-templates\templates\edit-post.php
  │  ✖ L40   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L57   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L61   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L65   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L109  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L228  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L240  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$card_index'.
  │  ✖ L249  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L299  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L308  WordPress.PHP.StrictInArray.MissingTrueStrict — Not using strict comparison for in_array; supply true for $strict argument.
  │  ✖ L309  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'mb_strlen'.
  │  ✖ L333  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$card_index'.
  │  ✖ L343  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L363  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L371  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L404  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L413  WordPress.PHP.StrictInArray.MissingTrueStrict — Not using strict comparison for in_array; supply true for $strict argument.
  │  ✖ L414  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'mb_strlen'.
  │  ✖ L500  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L503  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ⚠ L517  Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\templates\event\card-style-01.php
  │  ✖ L27   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $title
  │  ✖ L39   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $year
  │  ✖ L94   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L103  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L115  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L126  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L161  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L209  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$time_display'.
  │
  ├─ ...\apollo-templates\templates\page-classificados.php
  │  ⚠ L24   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L24   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L25   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L25   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L26   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $search
  │  ⚠ L26   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L26   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L27   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $paged
  │  ⚠ L27   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L27   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L87   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L91   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L94   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L94   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'APOLLO_TEMPLATES_URL'.
  │  ✖ L120  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L125  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L159  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L161  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$term'.
  │  ✖ L173  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$term'.
  │  ✖ L188  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L198  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$classifieds'.
  │  ✖ L200  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L233  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L259  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L268  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L272  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$wow_count'.
  │  ✖ L301  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L305  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'human_time_diff'.
  │  ⚠ L305  WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L315  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$author_id'.
  │  ✖ L316  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L321  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L339  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$comments_total'.
  │  ✖ L339  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L363  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$paged'.
  │  ✖ L363  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$classifieds'.
  │  ✖ L380  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L391  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │  ✖ L396  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L500  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ⚠ L518  Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\templates\page-home.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L26   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_home_before_content', but found: 'apollo/home/before_content'.
  │  ✖ L42   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L45   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L48   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ⚠ L232  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_home_head', but found: 'apollo/home/head'.
  │  ✖ L296  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ⚠ L353  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_home_after_content', but found: 'apollo/home/after_content'.
  │
  ├─ ...\apollo-templates\templates\page-mapa.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L78   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L473  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L474  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ⚠ L819  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_mapa_head', but found: 'apollo/mapa/head'.
  │  ✖ L891  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ⚠ L1186 WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_mapa_after_content', but found: 'apollo/mapa/after_content'.
  │
  ├─ ...\apollo-templates\templates\page-mural.php
  │  ⚠ L18   WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ✖ L22   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L27   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L31   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L38   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ⚠ L104  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L104  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L105  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L105  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L109  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ⚠ L174  WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L217  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L220  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L224  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L225  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L306  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │
  ├─ ...\apollo-templates\templates\page-sobre.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L34   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L39   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L40   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L41   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L44   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L48   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L49   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │
  ├─ ...\apollo-templates\templates\page-test.php
  │  ✖ L1616 WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L2151 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $comment
  │  ✖ L2154 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L2158 WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L2161 WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L2170 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$row_index'.
  │  ✖ L2172 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$display_url'.
  │  ✖ L2327 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │
  ├─ ...\apollo-templates\templates\template-parts\home\classifieds.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L78   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L80   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L81   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L129  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-templates\templates\template-parts\home\events-listing.php
  │  ✖ L14   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $per_page
  │  ✖ L15   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $orderby
  │  ✖ L16   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $order
  │  ✖ L103  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L126  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L137  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-templates\templates\template-parts\home\footer.php
  │  ✖ L65   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $link
  │  ✖ L67   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$link['attrs']'.
  │
  ├─ ...\apollo-templates\templates\template-parts\home\hero.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\home\hub-section.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L32   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L33   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-templates\templates\template-parts\home\infra.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\home\marquee.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\home\mission.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\home\tools-accordion.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\classifieds.php
  │  ✖ L33   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L60   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\favorites.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\greeting.php
  │  ⚠ L24   Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │  ✖ L45   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L54   Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L57   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L67   Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L70   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L78   Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L81   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L81   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L98   Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L101  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L110  Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace — Expected 1 space after closing brace; newline found
  │  ✖ L113  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L113  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L162  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L163  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L165  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\news.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L50   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post
  │  ✖ L59   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\same-vibe.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\sounds.php
  │  ✖ L26   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ⚠ L27   WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode — urlencode() should only be used when dealing with legacy applications rawurlencode() should now be used instead. See https://www.php.net/function.rawurlencode and http://www.faqs.org/rfcs/rfc3986.html
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\ticker.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\upcoming.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L136  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │
  ├─ ...\apollo-templates\templates\template-parts\navbar-old-backup.php
  │  ✖ L16   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L20   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-templates\templates\template-parts\navbar.php
  │  ✖ L18   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L24   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $names
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$bg_style'.
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon_style'.
  │  ✖ L322  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │  ✖ L338  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L342  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L371  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │
  ├─ ...\apollo-templates\templates\template-parts\navbar.v1.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected navbar-v1.php, but found navbar.v1.php.
  │  ✖ L18   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L24   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $names
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$bg_style'.
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon_style'.
  │  ✖ L322  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │  ✖ L338  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L342  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L371  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\classifieds.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\crash.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L85   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L93   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\events.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L116  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $status
  │  ✖ L123  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L128  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L130  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L130  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ⚠ L130  WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L133  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L137  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L193  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\footer.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L45   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\hero.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\map.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L50   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L61   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L62   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L63   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L167  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\marquee.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L23   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_home_marquee_items', but found: 'apollo/home/marquee_items'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\menu-fab.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\navbar.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L26   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-acesso.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L54   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_login_render_panel', but found: 'apollo/login/render_panel'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-chat-inbox.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L58   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_chat_render_thread', but found: 'apollo/chat/render_thread'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-chat-list.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L60   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_chat_render_list', but found: 'apollo/chat/render_list'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-chat.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L28   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_chat_render_panel', but found: 'apollo/chat/render_panel'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-detail.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L78   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_detail_render_panel', but found: 'apollo/detail/render_panel'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-dynamic.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L77   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_dynamic_after_content', but found: 'apollo/dynamic/after_content'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-event-page.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-explore.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L58   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_social_render_feed', but found: 'apollo/social/render_feed'.
  │  ⚠ L74   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_explore_after_feed', but found: 'apollo/explore/after_feed'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-forms.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L96   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_forms_extra_nav_items', but found: 'apollo/forms/extra_nav_items'.
  │  ⚠ L106  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_events_render_create_form', but found: 'apollo/events/render_create_form'.
  │  ⚠ L113  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_adverts_render_create_form', but found: 'apollo/adverts/render_create_form'.
  │  ⚠ L120  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_groups_render_create_form', but found: 'apollo/groups/render_create_form'.
  │  ⚠ L127  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_mod_render_report_form', but found: 'apollo/mod/render_report_form'.
  │  ⚠ L134  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_comment_render_depoimento_form', but found: 'apollo/comment/render_depoimento_form'.
  │  ⚠ L137  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_forms_extra_sections', but found: 'apollo/forms/extra_sections'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-mural.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L56   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_mural_after_content', but found: 'apollo/mural/after_content'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\panel-notif.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L28   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_notif_render_panel', but found: 'apollo/notif/render_panel'.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\radio.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\tracks.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L60   WordPress.WP.I18n.MissingTranslatorsComment — A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders.
  │  ✖ L71   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L74   WordPress.WP.AlternativeFunctions.rand_rand — rand() is discouraged. Use the far less predictable wp_rand() instead.
  │
  ├─ ...\apollo-templates\test-timezone.php
  │  ✖ L13   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'date'.
  │  ✖ L13   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L14   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'date_default_timezone_get'.
  │  ✖ L16   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ⚠ L17   WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L17   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L18   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L19   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L20   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L21   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L23   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_option'.
  │  ✖ L24   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_option'.
  │  ✖ L27   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$hour'.
  │  ✖ L39   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$greeting'.
  │
  ├─ ...\apollo-templates\_debug_test.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected -debug-test.php, but found _debug_test.php.
  │  ✖ L13   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"$p => $q\n"'.
  │  ✖ L18   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Found test rules: $found\n"'.
  │  ✖ L34   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"AFTER: $p => $q\n"'.
  │  ✖ L39   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Found after re-flush: $found2\n"'.
  └────────────────────────────────────────────

  ┌── apollo-templates [WordPress-VIP-Go] ──
  │   Errors: 132  Warnings: 60
  │
  ├─ ...\apollo-templates\apollo-templates.php
  │  ⚠ L318  WordPressVIPMinimum.UserExperience.AdminBarRemoval.RemovalDetected — Removal of admin bar is highly discouraged for user roles of "administrator" and "vip_support" -- if these roles are already excluded, this warning can be ignored.
  │  ⚠ L331  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['apollo_login_nonce']
  │  ⚠ L336  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['pass']
  │  ⚠ L373  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['pwd']
  │  ✖ L444  WordPressVIPMinimum.Variables.ServerVariables.UserControlledHeaders — Header "REMOTE_ADDR" is user-controlled and should be properly validated before use.
  │  ⚠ L444  WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__REMOTE_ADDR__ — Due to server-side caching, server-side based client related logic might not work. We recommend implementing client side logic in JavaScript instead.
  │  ✖ L512  WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L522  WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │
  ├─ ...\apollo-templates\create-page.php
  │  ✖ L11   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $page
  │  ✖ L29   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"✅ Página 'Classificados' criada com ID: $page_id<br>"'.
  │  ✖ L33   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$page_id'.
  │  ✖ L37   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"ℹ️ Página 'Classificados' já existe (ID: {$page->ID})<br>"'.
  │  ✖ L45   WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L48   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L48   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-templates\examples\user-radar-examples.php
  │  ⚠ L34   Squiz.PHP.CommentedOutCode.Found — This comment is 57% valid code; is this commented out code?
  │  ✖ L43   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"User is ranked #{$ranking}"'.
  │  ✖ L51   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Updated {$updated} user rankings"'.
  │  ✖ L56   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"{$user->display_name} - Ranking: "'.
  │  ✖ L56   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'UserRadar'.
  │  ⚠ L68   WordPress.PHP.DevelopmentFunctions.error_log_print_r — print_r() found. Debug code should not normally be used in production.
  │  ⚠ L69   Squiz.PHP.CommentedOutCode.Found — This comment is 49% valid code; is this commented out code?
  │  ✖ L341  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['before_widget']'.
  │  ✖ L342  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['before_title']'.
  │  ✖ L342  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['after_title']'.
  │  ✖ L352  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ranking'.
  │  ✖ L358  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$args['after_widget']'.
  │  ✖ L365  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$this'.
  │  ✖ L370  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$this'.
  │  ✖ L371  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$this'.
  │
  ├─ ...\apollo-templates\includes\class-shortcodes.php
  │  ⚠ L275  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L305  WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │
  ├─ ...\apollo-templates\includes\pages.php
  │  ⚠ L81   WordPress.WP.AlternativeFunctions.parse_url_parse_url — parse_url() is discouraged because of inconsistency in the output across PHP versions; use wp_parse_url() instead.
  │  ⚠ L81   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ⚠ L159  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['state']
  │  ✖ L187  WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L200  WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │
  ├─ ...\apollo-templates\includes\weather-helpers.php
  │  ⚠ L43   WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get — wp_remote_get() is highly discouraged. Please use vip_safe_wp_remote_get() instead which is designed to more gracefully handle failure than wp_remote_get() does.
  │  ✖ L46   WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout — Detected high remote request timeout. `timeout` is set to `10`.
  │  ✖ L52   WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L60   WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │
  ├─ ...\apollo-templates\src\Activation.php
  │  ✖ L51   WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ⚠ L119  WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents — File system operations only work on the `/tmp/` and `wp-content/uploads/` directories. To avoid unexpected results, please use helper functions like `get_temp_dir()`  or `wp_get_upload_dir()` to get the proper directory path when using functions such as file_put_contents(). For more details, please see: https://docs.wpvip.com/technical-references/vip-go-files-system/local-file-operations/
  │
  ├─ ...\apollo-templates\src\Deactivation.php
  │  ✖ L42   WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │
  ├─ ...\apollo-templates\src\FrontendEditor.php
  │  ✖ L485  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$current_len'.
  │  ✖ L485  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$maxlength'.
  │  ⚠ L569  Squiz.PHP.CommentedOutCode.Found — This comment is 40% valid code; is this commented out code?
  │  ⚠ L648  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['post_id']
  │  ⚠ L781  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['post_id']
  │  ⚠ L837  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST['post_id']
  │
  ├─ ...\apollo-templates\src\FrontendFields.php
  │  ⚠ L76   Squiz.PHP.CommentedOutCode.Found — This comment is 38% valid code; is this commented out code?
  │  ⚠ L496  Squiz.PHP.CommentedOutCode.Found — This comment is 58% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\src\FrontendRouter.php
  │  ⚠ L86   Squiz.PHP.CommentedOutCode.Found — This comment is 37% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\templates\auth\login-register.php
  │  ⚠ L168  Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\templates\edit-post.php
  │  ✖ L240  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$card_index'.
  │  ✖ L249  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L309  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'mb_strlen'.
  │  ✖ L333  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$card_index'.
  │  ✖ L343  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L414  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'mb_strlen'.
  │  ⚠ L517  Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\templates\event\card-style-01.php
  │  ✖ L27   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $title
  │  ✖ L39   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $year
  │  ✖ L94   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L103  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L115  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L161  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L209  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$time_display'.
  │
  ├─ ...\apollo-templates\templates\page-classificados.php
  │  ⚠ L24   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L24   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L25   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L25   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L26   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $search
  │  ⚠ L26   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L26   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L27   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $paged
  │  ⚠ L27   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L27   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L56   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ✖ L94   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'APOLLO_TEMPLATES_URL'.
  │  ✖ L120  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L125  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L159  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L161  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$term'.
  │  ✖ L173  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$term'.
  │  ✖ L188  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L198  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$classifieds'.
  │  ✖ L233  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L259  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L268  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L272  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$wow_count'.
  │  ✖ L301  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L305  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'human_time_diff'.
  │  ✖ L315  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$author_id'.
  │  ✖ L316  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$ad_id'.
  │  ✖ L321  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L339  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$comments_total'.
  │  ✖ L363  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$paged'.
  │  ✖ L363  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$classifieds'.
  │  ✖ L380  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L391  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │  ✖ L396  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L500  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ⚠ L518  Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\templates\page-mapa.php
  │  ⚠ L42   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L151  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │
  ├─ ...\apollo-templates\templates\page-mural.php
  │  ✖ L22   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L38   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ⚠ L63   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L83   WordPressVIPMinimum.Performance.OrderByRand.orderby_orderby — Detected forbidden query_var "orderby" of 'rand'. Use vip_get_random_posts() instead.
  │  ⚠ L84   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L92   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ⚠ L104  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L104  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L105  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L105  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L109  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ⚠ L119  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L142  WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ⚠ L157  WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │
  ├─ ...\apollo-templates\templates\page-test.php
  │  ✖ L2151 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $comment
  │  ✖ L2154 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │  ✖ L2170 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$row_index'.
  │  ✖ L2172 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$display_url'.
  │  ✖ L2327 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │
  ├─ ...\apollo-templates\templates\template-parts\home\classifieds.php
  │  ⚠ L21   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ⚠ L28   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L44   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ⚠ L51   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │
  ├─ ...\apollo-templates\templates\template-parts\home\events-listing.php
  │  ✖ L14   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $per_page
  │  ✖ L15   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $orderby
  │  ✖ L16   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $order
  │  ⚠ L27   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L103  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │
  ├─ ...\apollo-templates\templates\template-parts\home\footer.php
  │  ✖ L65   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $link
  │  ✖ L67   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$link['attrs']'.
  │
  ├─ ...\apollo-templates\templates\template-parts\home\hub-section.php
  │  ✖ L24   WordPressVIPMinimum.Performance.OrderByRand.orderby_orderby — Detected forbidden query_var "orderby" of 'rand'. Use vip_get_random_posts() instead.
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\classifieds.php
  │  ⚠ L14   Squiz.PHP.CommentedOutCode.Found — This comment is 36% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\greeting.php
  │  ⚠ L24   Squiz.PHP.CommentedOutCode.Found — This comment is 50% valid code; is this commented out code?
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\news.php
  │  ✖ L50   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\sounds.php
  │  ✖ L26   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │
  ├─ ...\apollo-templates\templates\template-parts\mural\upcoming.php
  │  ⚠ L12   Squiz.PHP.CommentedOutCode.Found — This comment is 37% valid code; is this commented out code?
  │  ✖ L136  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │
  ├─ ...\apollo-templates\templates\template-parts\navbar-old-backup.php
  │  ✖ L16   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │
  ├─ ...\apollo-templates\templates\template-parts\navbar.php
  │  ✖ L18   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L24   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $names
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$bg_style'.
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon_style'.
  │  ✖ L322  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │  ⚠ L353  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │
  ├─ ...\apollo-templates\templates\template-parts\navbar.v1.php
  │  ✖ L18   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │  ✖ L24   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $names
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$bg_style'.
  │  ✖ L222  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon_style'.
  │  ✖ L322  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │  ⚠ L353  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\crash.php
  │  ⚠ L29   WordPress.DB.SlowDBQuery.slow_db_query_tax_query — Detected usage of tax_query, possible slow query.
  │  ✖ L85   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $type
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\events.php
  │  ⚠ L27   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L116  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $status
  │  ✖ L123  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L130  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L133  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L193  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\footer.php
  │  ✖ L45   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\map.php
  │  ⚠ L29   WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L50   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L61   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L62   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L63   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\navbar.php
  │  ✖ L26   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $current_user
  │
  ├─ ...\apollo-templates\templates\template-parts\new-home\tracks.php
  │  ⚠ L74   WordPress.WP.AlternativeFunctions.rand_rand — rand() is discouraged. Use the far less predictable wp_rand() instead.
  │
  ├─ ...\apollo-templates\test-timezone.php
  │  ✖ L13   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'date'.
  │  ✖ L13   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L14   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'date_default_timezone_get'.
  │  ✖ L16   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L17   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L18   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L19   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L20   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L21   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'current_time'.
  │  ✖ L23   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_option'.
  │  ✖ L24   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_option'.
  │  ✖ L27   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$hour'.
  │  ✖ L39   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$greeting'.
  │
  ├─ ...\apollo-templates\_debug_test.php
  │  ✖ L6    WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L13   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"$p => $q\n"'.
  │  ✖ L18   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Found test rules: $found\n"'.
  │  ✖ L26   WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L34   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"AFTER: $p => $q\n"'.
  │  ✖ L39   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Found after re-flush: $found2\n"'.
  └────────────────────────────────────────────

  ┌── apollo-users [WordPress] ──
  │   Errors: 546  Warnings: 95
  │
  ├─ ...\apollo-users\apollo-users.php
  │  ⚠ L23   WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting — error_reporting() can lead to full path disclosure.
  │  ⚠ L23   WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting — error_reporting() found. Changing configuration values at runtime is strongly discouraged.
  │  ✖ L24   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_SERVER['REQUEST_URI'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L24   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ⚠ L25   WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting — error_reporting() can lead to full path disclosure.
  │  ⚠ L25   WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting — error_reporting() found. Changing configuration values at runtime is strongly discouraged.
  │  ✖ L48   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L60   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L92   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$message" missing
  │  ✖ L92   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$error_type" missing
  │  ⚠ L95   Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $error_type is never used
  │  ✖ L97   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L108  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L113  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L115  Universal.NamingConventions.NoReservedKeywordParameterNames.classFound — It is recommended not to use reserved keyword "class" as function parameter name. Found: $class
  │  ✖ L144  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$vars" missing
  │  ✖ L159  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L162  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L165  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L170  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$wp" missing
  │  ⚠ L173  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $wp is never used
  │  ⚠ L174  WordPress.WP.AlternativeFunctions.parse_url_parse_url — parse_url() is discouraged because of inconsistency in the output across PHP versions; use wp_parse_url() instead.
  │  ✖ L174  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_SERVER['REQUEST_URI'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L174  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L176  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L177  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L178  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L181  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L187  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L190  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L196  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L200  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L201  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L214  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L215  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L217  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L219  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L269  Squiz.PHP.Eval.Discouraged — eval() is a security risk so not allowed.
  │  ✖ L272  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L278  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L279  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L283  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L284  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L286  WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ✖ L294  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L296  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L346  Squiz.PHP.Eval.Discouraged — eval() is a security risk so not allowed.
  │  ✖ L365  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L366  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_SERVER['REQUEST_URI'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L366  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L371  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L381  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L382  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L385  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L390  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L402  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L410  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L413  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\bin\recalculate-profile-completion.php
  │  ✖ L11   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L13   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L28   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L33   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Found {$total} users to process...\n\n"'.
  │  ✖ L35   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $plugin
  │  ✖ L40   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L65   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L68   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"✓ User #{$user_id} ({$user->user_login}): {$percentage}% complete\n"'.
  │  ✖ L75   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Total users processed: {$updated}/{$total}\n"'.
  │
  ├─ ...\apollo-users\diagnostic.php
  │  ✖ L7    Squiz.Commenting.FileComment.MissingPackageTag — Missing @package tag in file comment
  │  ✖ L9    Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L58   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"  $pattern => $rule\n"'.
  │  ✖ L71   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L72   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L73   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\flush-cli.php
  │  ✖ L6    Squiz.Commenting.FileComment.MissingPackageTag — Missing @package tag in file comment
  │  ✖ L8    Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L10   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L11   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L16   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $path
  │  ✖ L28   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L33   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\flush-rewrite-rules.php
  │  ✖ L11   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L14   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L19   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L22   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L25   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L29   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L30   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L31   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L34   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\flush-rewrites.php
  │  ✖ L6    Squiz.Commenting.FileComment.MissingPackageTag — Missing @package tag in file comment
  │  ✖ L8    Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L11   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L16   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L21   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L27   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L28   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\flush.php
  │  ✖ L7    Squiz.Commenting.FileComment.MissingPackageTag — Missing @package tag in file comment
  │  ✖ L9    Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L12   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L22   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L27   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L48   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L49   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\includes\constants.php
  │  ✖ L17   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L20   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L26   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L29   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L43   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\includes\functions.php
  │  ✖ L29   Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L52   Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L58   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L64   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L65   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L65   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L82   Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L83   Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L87   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L97   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L103  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L109  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L116  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L133  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L187  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L188  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L202  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L216  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L217  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L223  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L230  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ✖ L236  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_SERVER['REMOTE_ADDR'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L236  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REMOTE_ADDR']
  │  ✖ L242  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L250  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L254  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L260  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L268  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L277  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L285  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L332  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L342  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L348  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L361  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L367  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L378  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L379  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L394  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L418  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L438  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L450  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L457  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L468  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L475  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L488  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L495  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L497  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\setup-registry-compliance.php
  │  ✖ L13   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L16   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L21   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L22   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $action
  │  ⚠ L22   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L22   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_GET['action'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L22   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_GET['action']
  │  ✖ L85   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L90   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L93   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L109  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L112  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $role
  │  ⚠ L119  WordPress.PHP.DevelopmentFunctions.error_log_print_r — print_r() found. Debug code should not normally be used in production.
  │  ✖ L119  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'print_r'.
  │  ✖ L124  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L133  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $plugin
  │  ✖ L135  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$total'.
  │  ✖ L141  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L166  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L169  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$user_id'.
  │  ✖ L169  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$percentage'.
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$updated'.
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$total'.
  │  ✖ L180  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L197  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L200  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $role
  │  ✖ L207  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L223  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$users_count['total_users']'.
  │
  ├─ ...\apollo-users\setup.php
  │  ✖ L7    Squiz.Commenting.FileComment.MissingPackageTag — Missing @package tag in file comment
  │  ✖ L9    Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L21   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L29   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L39   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L49   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L78   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<div class='message $class'>$message</div>"'.
  │  ✖ L83   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L84   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\src\Activation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected activation.php, but found Activation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-activation.php, but found Activation.php.
  │  ✖ L28   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L31   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L34   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L37   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L40   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L57   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L72   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L89   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L128  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L132  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L148  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L168  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L185  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L203  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L224  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L256  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L269  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\src\API\ProfileController.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected profilecontroller.php, but found ProfileController.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-profilecontroller.php, but found ProfileController.php.
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L35   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L44   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L55   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L66   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L77   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L88   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L105  Squiz.PHP.CommentedOutCode.Found — This comment is 45% valid code; is this commented out code?
  │  ✖ L120  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L122  Squiz.PHP.CommentedOutCode.Found — This comment is 52% valid code; is this commented out code?
  │  ✖ L126  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L159  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L171  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L177  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L196  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L245  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L257  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L263  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L281  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L312  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L313  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L313  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L313  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ⚠ L317  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L317  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L320  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ✖ L366  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L375  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L386  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L387  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L387  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L387  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ✖ L395  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L403  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L404  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L404  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L414  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L415  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L415  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L417  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT id FROM {$table}\n
  │  ✖ L428  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L449  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L455  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L456  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L456  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L456  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ✖ L460  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L461  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L461  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L464  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} m1\n
  │  ✖ L465  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              INNER JOIN {$table} m2 ON m1.user_id = m2.target_user_id AND m1.target_user_id = m2.user_id\n
  │
  ├─ ...\apollo-users\src\API\UsersController.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected userscontroller.php, but found UsersController.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-userscontroller.php, but found UsersController.php.
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L35   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L44   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L55   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L66   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L91   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L98   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L116  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L134  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L167  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L185  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L203  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L221  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L239  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L257  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L275  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L293  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L311  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L345  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L355  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L371  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L389  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L418  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L430  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L431  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L433  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L441  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L449  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L463  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L476  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L477  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L479  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L485  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L492  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L498  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L500  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ✖ L502  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L515  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L524  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L573  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L578  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L586  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L592  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L595  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L614  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L621  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L622  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L622  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L622  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ✖ L626  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L627  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L627  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L631  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L669  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L711  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L729  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L771  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L884  Universal.Files.SeparateFunctionsFromOO.Mixed — A file should either contain function declarations or OO structure declarations, but not both. Found 1 function declaration(s) and 1 OO structure declaration(s). The first function declaration was found on line 884; the first OO declaration was found on line 21
  │  ✖ L889  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_SERVER[$key] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L889  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER[$key]
  │
  ├─ ...\apollo-users\src\Components\AuthorProtection.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected authorprotection.php, but found AuthorProtection.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-authorprotection.php, but found AuthorProtection.php.
  │  ✖ L28   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L31   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L34   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L37   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L40   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L43   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L53   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L54   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L54   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L55   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L55   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L59   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L60   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L60   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_GET['author_name'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L63   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L69   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L91   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L104  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L106  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L112  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L126  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L139  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $error is never used
  │  ✖ L142  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L157  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\src\Components\DepoimentoHandler.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected depoimentohandler.php, but found DepoimentoHandler.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-depoimentohandler.php, but found DepoimentoHandler.php.
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L21   Squiz.Commenting.ClassComment.Missing — Missing doc comment for class DepoimentoHandler
  │  ✖ L26   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function __construct()
  │  ✖ L27   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L31   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L43   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['text'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L58   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L89   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L94   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L143  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L161  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$query" missing
  │
  ├─ ...\apollo-users\src\Components\ProfileHandler.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected profilehandler.php, but found ProfileHandler.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-profilehandler.php, but found ProfileHandler.php.
  │  ✖ L26   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L48   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L50   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['social_name'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L51   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['bio'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L52   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['website'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L53   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['youtube_url'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L54   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['soundcloud_url'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L55   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['phone'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L56   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['location'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L57   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L58   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['privacy_profile'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L66   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L105  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L112  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L118  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L152  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L159  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L165  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\src\Components\RatingHandler.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected ratinghandler.php, but found RatingHandler.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-ratinghandler.php, but found RatingHandler.php.
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L22   Squiz.Commenting.ClassComment.Missing — Missing doc comment for class RatingHandler
  │  ✖ L51   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function __construct()
  │  ✖ L52   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L57   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L74   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['category'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L77   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L97   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L98   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L98   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L100  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (voter_id, target_id, category, score, created_at, updated_at)\n
  │  ✖ L111  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L124  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L124  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L150  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L150  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L158  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L158  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L162  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} r\n
  │  ⚠ L199  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L199  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L238  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$target_id" missing
  │  ⚠ L247  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L247  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L250  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ✖ L271  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$voter_id" missing
  │  ✖ L271  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$target_id" missing
  │  ⚠ L280  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L280  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L282  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT category, score FROM {$table}\n
  │  ✖ L303  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$target_id" missing
  │  ⚠ L310  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L310  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L312  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COUNT(DISTINCT voter_id) FROM {$table} WHERE target_id = %d"
  │  ✖ L318  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$target_id" missing
  │  ⚠ L327  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L327  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L330  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table}\n
  │  ⚠ L364  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L364  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L367  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} r\n
  │  ⚠ L412  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L412  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L414  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COUNT(*) FROM {$table}\n
  │  ⚠ L421  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L421  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L425  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} r\n
  │  ✖ L450  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L472  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L472  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L473  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L473  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_GET['category'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L473  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ✖ L473  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST['category'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ⚠ L474  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L480  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L485  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\src\Components\UserFields.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected userfields.php, but found UserFields.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-userfields.php, but found UserFields.php.
  │  ✖ L37   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L41   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L168  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L176  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L208  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ✖ L209  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ✖ L209  WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_POST[$key] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L209  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST[$key]
  │  ✖ L211  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L212  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\src\Deactivation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected deactivation.php, but found Deactivation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-deactivation.php, but found Deactivation.php.
  │  ✖ L28   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L32   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\src\Plugin.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected plugin.php, but found Plugin.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-plugin.php, but found Plugin.php.
  │  ✖ L45   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L54   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L59   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L63   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L68   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L103  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L111  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L119  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L162  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L163  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L164  WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ✖ L168  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L182  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L183  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L197  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L240  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L262  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L289  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L296  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L321  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L356  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L357  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L358  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L359  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ⚠ L362  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $meta_value is never used
  │  ✖ L363  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L385  Squiz.Commenting.FunctionComment.ParamCommentFullStop — Parameter comment must end with a full stop
  │  ✖ L389  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L398  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\templates\edit-profile.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ⚠ L20   WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ⚠ L20   WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode — urlencode() should only be used when dealing with legacy applications rawurlencode() should now be used instead. See https://www.php.net/function.rawurlencode and http://www.faqs.org/rfcs/rfc3986.html
  │  ✖ L29   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L33   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L34   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L35   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L64   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L99   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L103  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L104  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L106  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L111  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L123  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L124  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L127  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L1241 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'strlen'.
  │  ✖ L1277 Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L1311 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon'.
  │  ✖ L1311 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$filled'.
  │  ✖ L1323 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon'.
  │  ✖ L1323 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$filled'.
  │  ✖ L1336 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon'.
  │  ✖ L1336 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$filled'.
  │  ✖ L1419 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post_type
  │  ✖ L1425 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_edit_post_link'.
  │  ✖ L1429 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_the_post_thumbnail_url'.
  │  ✖ L1435 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_trim_words'.
  │
  ├─ ...\apollo-users\templates\parts\matchmaking-widget.php
  │  ✖ L38   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │
  ├─ ...\apollo-users\templates\parts\profile-display.php
  │  ✖ L20   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-users\templates\parts\profile-edit-form.php
  │  ✖ L18   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L25   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L90   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'admin_url'.
  │
  ├─ ...\apollo-users\templates\parts\profile-feed.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L14   Squiz.PHP.CommentedOutCode.Found — This comment is 56% valid code; is this commented out code?
  │  ✖ L14   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L16   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L41   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post_type
  │  ✖ L47   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L64   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-users\templates\parts\profile-fields.php
  │  ✖ L16   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L16   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L23   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L65   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L76   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L80   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-users\templates\parts\profile-hero.php
  │  ⚠ L12   Squiz.PHP.CommentedOutCode.Found — This comment is 59% valid code; is this commented out code?
  │  ✖ L12   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\templates\parts\profile-sidebar.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L16   Squiz.PHP.CommentedOutCode.Found — This comment is 44% valid code; is this commented out code?
  │  ✖ L17   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L49   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L52   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L74   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L82   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L116  WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L129  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\templates\parts\user-card.php
  │  ✖ L20   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-users\templates\parts\user-radar.php
  │  ✖ L17   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $role
  │  ✖ L18   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $orderby
  │  ✖ L42   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-users\templates\profile-login-required.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L16   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L17   WordPress.Security.ValidatedSanitizedInput.InputNotValidated — Detected usage of a possibly undefined superglobal array index: $_SERVER['REQUEST_URI']. Check that the array index exists before using it.
  │  ✖ L17   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_SERVER['REQUEST_URI'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L17   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L19   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L27   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_bloginfo'.
  │  ✖ L30   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L34   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L35   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L42   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L60   WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode — urlencode() should only be used when dealing with legacy applications rawurlencode() should now be used instead. See https://www.php.net/function.rawurlencode and http://www.faqs.org/rfcs/rfc3986.html
  │
  ├─ ...\apollo-users\templates\profile-private.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L16   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L24   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_bloginfo'.
  │  ✖ L27   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L31   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L32   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L39   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\templates\single-profile.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L33   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L34   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L35   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L39   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L44   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L46   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L47   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L48   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L60   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L79   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L84   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L93   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L96   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L98   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L104  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L108  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L110  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L111  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L126  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L131  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L138  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L159  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L169  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L189  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L192  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L195  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L199  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L1768 Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L1803 Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L1807 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$badge_html'.
  │  ✖ L1818 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L1895 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$i'.
  │  ✖ L1908 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$i'.
  │  ✖ L1922 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$i'.
  │  ✖ L2045 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post
  │  ⚠ L2049 WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L2070 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post
  │  ✖ L2122 Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L2147 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$depo_badge_html'.
  │
  ├─ ...\apollo-users\templates\user-radar.php
  │  ✖ L1    Squiz.Commenting.FileComment.SpacingAfterOpen — There must be no blank lines before the file comment
  │  ✖ L47   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L347  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L352  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L416  WordPress.DB.SlowDBQuery.slow_db_query_meta_key — Detected usage of meta_key, possible slow query.
  │  ✖ L439  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L444  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-users\test-radar.php
  │  ✖ L4    Squiz.Commenting.FileComment.MissingPackageTag — Missing @package tag in file comment
  │  ✖ L6    Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L11   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L12   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $page
  │  ✖ L13   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>apollo_user_page query var: <strong>$page</strong></p>"'.
  │  ✖ L15   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L16   WordPress.Security.ValidatedSanitizedInput.MissingUnslash — $_SERVER['REQUEST_URI'] not unslashed before sanitization. Use wp_unslash() or similar
  │  ✖ L16   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L17   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>Current URL: <strong>$current_url</strong></p>"'.
  │  ✖ L19   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L21   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>User logged in: <strong>$logged_in</strong></p>"'.
  │  ✖ L23   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L25   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>Template: <strong>$template</strong></p>"'.
  │
  ├─ ...\apollo-users\uninstall.php
  │  ✖ L15   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L19   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L23   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L34   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ⚠ L48   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L48   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L57   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  └────────────────────────────────────────────

  ┌── apollo-users [WordPress-Extra] ──
  │   Errors: 171  Warnings: 47
  │
  ├─ ...\apollo-users\apollo-users.php
  │  ⚠ L23   WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting — error_reporting() can lead to full path disclosure.
  │  ⚠ L23   WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting — error_reporting() found. Changing configuration values at runtime is strongly discouraged.
  │  ⚠ L25   WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting — error_reporting() can lead to full path disclosure.
  │  ⚠ L25   WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting — error_reporting() found. Changing configuration values at runtime is strongly discouraged.
  │  ⚠ L95   Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $error_type is never used
  │  ⚠ L115  Universal.NamingConventions.NoReservedKeywordParameterNames.classFound — It is recommended not to use reserved keyword "class" as function parameter name. Found: $class
  │  ⚠ L173  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $wp is never used
  │  ⚠ L174  WordPress.WP.AlternativeFunctions.parse_url_parse_url — parse_url() is discouraged because of inconsistency in the output across PHP versions; use wp_parse_url() instead.
  │  ✖ L177  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L201  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L215  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L269  Squiz.PHP.Eval.Discouraged — eval() is a security risk so not allowed.
  │  ⚠ L279  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L284  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L286  WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ✖ L346  Squiz.PHP.Eval.Discouraged — eval() is a security risk so not allowed.
  │  ⚠ L381  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L390  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │
  ├─ ...\apollo-users\bin\recalculate-profile-completion.php
  │  ✖ L33   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Found {$total} users to process...\n\n"'.
  │  ✖ L35   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $plugin
  │  ✖ L68   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"✓ User #{$user_id} ({$user->user_login}): {$percentage}% complete\n"'.
  │  ✖ L75   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Total users processed: {$updated}/{$total}\n"'.
  │
  ├─ ...\apollo-users\diagnostic.php
  │  ✖ L58   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"  $pattern => $rule\n"'.
  │  ✖ L71   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L72   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L73   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\flush-cli.php
  │  ✖ L16   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $path
  │
  ├─ ...\apollo-users\flush-rewrite-rules.php
  │  ✖ L29   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L30   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L31   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L34   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\flush-rewrites.php
  │  ✖ L27   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L28   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\flush.php
  │  ✖ L48   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L49   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\includes\functions.php
  │  ✖ L361  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L457  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L475  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L495  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │
  ├─ ...\apollo-users\setup-registry-compliance.php
  │  ✖ L22   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $action
  │  ⚠ L22   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L85   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L112  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $role
  │  ⚠ L119  WordPress.PHP.DevelopmentFunctions.error_log_print_r — print_r() found. Debug code should not normally be used in production.
  │  ✖ L119  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'print_r'.
  │  ✖ L124  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L133  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $plugin
  │  ✖ L135  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$total'.
  │  ✖ L169  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$user_id'.
  │  ✖ L169  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$percentage'.
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$updated'.
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$total'.
  │  ✖ L200  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $role
  │  ✖ L223  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$users_count['total_users']'.
  │
  ├─ ...\apollo-users\setup.php
  │  ✖ L78   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<div class='message $class'>$message</div>"'.
  │  ✖ L83   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L84   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\src\Activation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected activation.php, but found Activation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-activation.php, but found Activation.php.
  │
  ├─ ...\apollo-users\src\API\ProfileController.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected profilecontroller.php, but found ProfileController.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-profilecontroller.php, but found ProfileController.php.
  │  ⚠ L105  Squiz.PHP.CommentedOutCode.Found — This comment is 45% valid code; is this commented out code?
  │  ⚠ L122  Squiz.PHP.CommentedOutCode.Found — This comment is 52% valid code; is this commented out code?
  │  ⚠ L196  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ⚠ L281  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L313  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ✖ L320  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ✖ L387  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ✖ L417  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT id FROM {$table}\n
  │  ⚠ L449  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L456  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ✖ L464  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} m1\n
  │  ✖ L465  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              INNER JOIN {$table} m2 ON m1.user_id = m2.target_user_id AND m1.target_user_id = m2.user_id\n
  │
  ├─ ...\apollo-users\src\API\UsersController.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected userscontroller.php, but found UsersController.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-userscontroller.php, but found UsersController.php.
  │  ⚠ L355  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $request is never used
  │  ✖ L431  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L433  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L441  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L595  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L622  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ✖ L631  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L884  Universal.Files.SeparateFunctionsFromOO.Mixed — A file should either contain function declarations or OO structure declarations, but not both. Found 1 function declaration(s) and 1 OO structure declaration(s). The first function declaration was found on line 884; the first OO declaration was found on line 21
  │
  ├─ ...\apollo-users\src\Components\AuthorProtection.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected authorprotection.php, but found AuthorProtection.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-authorprotection.php, but found AuthorProtection.php.
  │  ⚠ L54   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L54   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L55   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L55   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L59   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L60   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L139  Generic.CodeAnalysis.UnusedFunctionParameter.Found — The method parameter $error is never used
  │
  ├─ ...\apollo-users\src\Components\DepoimentoHandler.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected depoimentohandler.php, but found DepoimentoHandler.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-depoimentohandler.php, but found DepoimentoHandler.php.
  │  ✖ L94   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L143  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-users\src\Components\ProfileHandler.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected profilehandler.php, but found ProfileHandler.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-profilehandler.php, but found ProfileHandler.php.
  │
  ├─ ...\apollo-users\src\Components\RatingHandler.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected ratinghandler.php, but found RatingHandler.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-ratinghandler.php, but found RatingHandler.php.
  │  ✖ L100  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (voter_id, target_id, category, score, created_at, updated_at)\n
  │  ⚠ L124  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L124  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L150  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L150  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ✖ L162  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table} r\n
  │  ✖ L250  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at              FROM {$table}\n
  │  ✖ L282  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT category, score FROM {$table}\n
  │  ✖ L312  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COUNT(DISTINCT voter_id) FROM {$table} WHERE target_id = %d"
  │  ✖ L330  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table}\n
  │  ✖ L367  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} r\n
  │  ✖ L414  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COUNT(*) FROM {$table}\n
  │  ✖ L425  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at                  FROM {$table} r\n
  │  ✖ L450  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L472  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L472  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L473  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L473  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L474  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │
  ├─ ...\apollo-users\src\Components\UserFields.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected userfields.php, but found UserFields.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-userfields.php, but found UserFields.php.
  │  ✖ L168  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L176  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L208  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ✖ L209  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ✖ L211  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-users\src\Deactivation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected deactivation.php, but found Deactivation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-deactivation.php, but found Deactivation.php.
  │
  ├─ ...\apollo-users\src\Plugin.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected plugin.php, but found Plugin.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-plugin.php, but found Plugin.php.
  │  ⚠ L163  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L164  WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ⚠ L362  Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed — The method parameter $meta_value is never used
  │
  ├─ ...\apollo-users\templates\edit-profile.php
  │  ⚠ L20   WordPress.Security.SafeRedirect.wp_redirect_wp_redirect — wp_redirect() found. Using wp_safe_redirect(), along with the "allowed_redirect_hosts" filter if needed, can help avoid any chances of malicious redirects within code. It is also important to remember to call exit() after a redirect so that no other unwanted code is executed.
  │  ⚠ L20   WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode — urlencode() should only be used when dealing with legacy applications rawurlencode() should now be used instead. See https://www.php.net/function.rawurlencode and http://www.faqs.org/rfcs/rfc3986.html
  │  ✖ L29   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L33   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L34   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L35   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L64   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L99   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L104  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L123  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L124  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L127  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L1241 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'strlen'.
  │  ✖ L1311 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon'.
  │  ✖ L1311 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$filled'.
  │  ✖ L1323 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon'.
  │  ✖ L1323 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$filled'.
  │  ✖ L1336 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon'.
  │  ✖ L1336 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$filled'.
  │  ✖ L1419 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post_type
  │  ✖ L1425 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_edit_post_link'.
  │  ✖ L1429 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_the_post_thumbnail_url'.
  │  ✖ L1435 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_trim_words'.
  │
  ├─ ...\apollo-users\templates\parts\matchmaking-widget.php
  │  ✖ L38   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │
  ├─ ...\apollo-users\templates\parts\profile-display.php
  │  ✖ L20   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-users\templates\parts\profile-edit-form.php
  │  ✖ L25   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L90   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'admin_url'.
  │
  ├─ ...\apollo-users\templates\parts\profile-feed.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L14   Squiz.PHP.CommentedOutCode.Found — This comment is 56% valid code; is this commented out code?
  │  ✖ L41   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post_type
  │  ✖ L47   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L64   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-users\templates\parts\profile-fields.php
  │  ✖ L16   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L16   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L65   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L76   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L80   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │
  ├─ ...\apollo-users\templates\parts\profile-hero.php
  │  ⚠ L12   Squiz.PHP.CommentedOutCode.Found — This comment is 59% valid code; is this commented out code?
  │
  ├─ ...\apollo-users\templates\parts\profile-sidebar.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ⚠ L16   Squiz.PHP.CommentedOutCode.Found — This comment is 44% valid code; is this commented out code?
  │  ✖ L74   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L82   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ⚠ L116  WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │
  ├─ ...\apollo-users\templates\parts\user-card.php
  │  ✖ L20   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-users\templates\parts\user-radar.php
  │  ✖ L17   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $role
  │  ✖ L18   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $orderby
  │  ✖ L42   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-users\templates\profile-login-required.php
  │  ✖ L27   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_bloginfo'.
  │  ✖ L30   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L34   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L35   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ⚠ L60   WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode — urlencode() should only be used when dealing with legacy applications rawurlencode() should now be used instead. See https://www.php.net/function.rawurlencode and http://www.faqs.org/rfcs/rfc3986.html
  │
  ├─ ...\apollo-users\templates\profile-private.php
  │  ✖ L24   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_bloginfo'.
  │  ✖ L27   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L31   WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L32   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │
  ├─ ...\apollo-users\templates\single-profile.php
  │  ✖ L34   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L35   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L39   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L46   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L47   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L48   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L84   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L98   Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L110  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L189  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L192  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L195  WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │  ✖ L199  WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet — Stylesheets must be registered/enqueued via wp_enqueue_style()
  │  ✖ L1807 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$badge_html'.
  │  ✖ L1818 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L1895 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$i'.
  │  ✖ L1908 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$i'.
  │  ✖ L1922 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$i'.
  │  ✖ L2045 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post
  │  ⚠ L2049 WordPress.DateTime.CurrentTimeTimestamp.Requested — Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.
  │  ✖ L2070 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post
  │  ✖ L2122 Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L2147 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$depo_badge_html'.
  │
  ├─ ...\apollo-users\templates\user-radar.php
  │  ✖ L47   WordPress.WP.EnqueuedResources.NonEnqueuedScript — Scripts must be registered/enqueued via wp_enqueue_script()
  │
  ├─ ...\apollo-users\test-radar.php
  │  ✖ L12   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $page
  │  ✖ L13   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>apollo_user_page query var: <strong>$page</strong></p>"'.
  │  ✖ L17   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>Current URL: <strong>$current_url</strong></p>"'.
  │  ✖ L21   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>User logged in: <strong>$logged_in</strong></p>"'.
  │  ✖ L25   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>Template: <strong>$template</strong></p>"'.
  └────────────────────────────────────────────

  ┌── apollo-users [WordPress-VIP-Go] ──
  │   Errors: 117  Warnings: 80
  │
  ├─ ...\apollo-users\apollo-users.php
  │  ✖ L23   WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting — error_reporting() found. Changing configuration values at runtime is strongly discouraged.
  │  ⚠ L24   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L25   WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting — error_reporting() found. Changing configuration values at runtime is strongly discouraged.
  │  ⚠ L174  WordPress.WP.AlternativeFunctions.parse_url_parse_url — parse_url() is discouraged because of inconsistency in the output across PHP versions; use wp_parse_url() instead.
  │  ⚠ L174  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L201  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L209  VariableAnalysis.CodeAnalysis.VariableAnalysis.VariableRedeclaration — Redeclaration of global variable $wp_query as global variable.
  │  ✖ L215  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L269  Squiz.PHP.Eval.Discouraged — `eval()` is a security risk, please refrain from using it.
  │  ✖ L279  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ⚠ L290  VariableAnalysis.CodeAnalysis.VariableAnalysis.VariableRedeclaration — Redeclaration of global variable $wp_query as global variable.
  │  ✖ L346  Squiz.PHP.Eval.Discouraged — `eval()` is a security risk, please refrain from using it.
  │  ⚠ L366  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L381  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L390  WordPress.PHP.DevelopmentFunctions.error_log_error_log — error_log() found. Debug code should not normally be used in production.
  │  ✖ L415  WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L423  WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │
  ├─ ...\apollo-users\bin\recalculate-profile-completion.php
  │  ✖ L33   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Found {$total} users to process...\n\n"'.
  │  ✖ L35   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $plugin
  │  ✖ L68   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"✓ User #{$user_id} ({$user->user_login}): {$percentage}% complete\n"'.
  │  ✖ L75   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"Total users processed: {$updated}/{$total}\n"'.
  │
  ├─ ...\apollo-users\diagnostic.php
  │  ✖ L58   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"  $pattern => $rule\n"'.
  │  ✖ L71   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L72   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L73   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\flush-cli.php
  │  ✖ L16   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $path
  │  ✖ L34   WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │
  ├─ ...\apollo-users\flush-rewrite-rules.php
  │  ✖ L23   WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L29   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L30   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L31   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L34   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\flush-rewrites.php
  │  ✖ L23   WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rules_flush_rules — `flush_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L27   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L28   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\flush.php
  │  ✖ L28   WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L48   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L49   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\includes\functions.php
  │  ⚠ L65   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L65   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L67   WordPressVIPMinimum.Variables.RestrictedVariables.user_meta__wpdb__users — Usage of users tables is highly discouraged in VIP context
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L236  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REMOTE_ADDR']
  │  ✖ L236  WordPressVIPMinimum.Variables.ServerVariables.UserControlledHeaders — Header "REMOTE_ADDR" is user-controlled and should be properly validated before use.
  │  ⚠ L236  WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__REMOTE_ADDR__ — Due to server-side caching, server-side based client related logic might not work. We recommend implementing client side logic in JavaScript instead.
  │  ✖ L495  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │
  ├─ ...\apollo-users\setup-registry-compliance.php
  │  ✖ L22   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $action
  │  ⚠ L22   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L22   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_GET['action']
  │  ✖ L112  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $role
  │  ⚠ L119  WordPress.PHP.DevelopmentFunctions.error_log_print_r — print_r() found. Debug code should not normally be used in production.
  │  ✖ L119  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'print_r'.
  │  ✖ L133  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $plugin
  │  ✖ L135  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$total'.
  │  ✖ L169  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$user_id'.
  │  ✖ L169  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$percentage'.
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$updated'.
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$total'.
  │  ⚠ L198  VariableAnalysis.CodeAnalysis.VariableAnalysis.VariableRedeclaration — Redeclaration of global variable $wp_roles as global variable.
  │  ✖ L200  WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $role
  │  ✖ L223  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$users_count['total_users']'.
  │
  ├─ ...\apollo-users\setup.php
  │  ✖ L50   WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules — `flush_rewrite_rules` should not be used in any normal circumstances in the theme code.
  │  ✖ L78   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<div class='message $class'>$message</div>"'.
  │  ✖ L83   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │  ✖ L84   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'.
  │
  ├─ ...\apollo-users\src\Activation.php
  │  ✖ L133  WordPressVIPMinimum.Functions.RestrictedFunctions.custom_role_add_role — Use wpcom_vip_add_role() instead of add_role().
  │  ✖ L149  WordPressVIPMinimum.Functions.RestrictedFunctions.custom_role_add_role — Use wpcom_vip_add_role() instead of add_role().
  │  ✖ L169  WordPressVIPMinimum.Functions.RestrictedFunctions.custom_role_add_role — Use wpcom_vip_add_role() instead of add_role().
  │  ✖ L186  WordPressVIPMinimum.Functions.RestrictedFunctions.custom_role_add_role — Use wpcom_vip_add_role() instead of add_role().
  │  ✖ L204  WordPressVIPMinimum.Functions.RestrictedFunctions.custom_role_add_role — Use wpcom_vip_add_role() instead of add_role().
  │
  ├─ ...\apollo-users\src\API\ProfileController.php
  │  ⚠ L105  Squiz.PHP.CommentedOutCode.Found — This comment is 45% valid code; is this commented out code?
  │  ⚠ L122  Squiz.PHP.CommentedOutCode.Found — This comment is 52% valid code; is this commented out code?
  │  ⚠ L313  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L313  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L313  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ⚠ L317  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L317  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L320  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at \t\t\t FROM {$table}\n
  │  ⚠ L387  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L387  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L387  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ⚠ L404  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L404  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L415  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L415  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L417  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT id FROM {$table}\n
  │  ⚠ L456  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L456  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L456  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ⚠ L461  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L461  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L464  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at \t\t\t FROM {$table} m1\n
  │  ✖ L465  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at \t\t\t INNER JOIN {$table} m2 ON m1.user_id = m2.target_user_id AND m1.target_user_id = m2.user_id\n
  │
  ├─ ...\apollo-users\src\API\UsersController.php
  │  ⚠ L477  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L500  WordPress.DB.SlowDBQuery.slow_db_query_meta_query — Detected usage of meta_query, possible slow query.
  │  ⚠ L622  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L622  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L622  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SHOW TABLES LIKE '{$table}'"
  │  ⚠ L627  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L627  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L889  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER[$key]
  │
  ├─ ...\apollo-users\src\Components\AuthorProtection.php
  │  ⚠ L54   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L54   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L55   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L55   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L59   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ⚠ L60   WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │
  ├─ ...\apollo-users\src\Components\RatingHandler.php
  │  ⚠ L98   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L98   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L100  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT INTO {$table} (voter_id, target_id, category, score, created_at, updated_at)\n
  │  ⚠ L124  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L124  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L150  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L150  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L158  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L158  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L162  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at \t\t\t FROM {$table} r\n
  │  ✖ L163  WordPressVIPMinimum.Variables.RestrictedVariables.user_meta__wpdb__users — Usage of users tables is highly discouraged in VIP context
  │  ⚠ L199  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L199  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L231  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L247  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L247  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L250  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at \t\t\t FROM {$table}\n
  │  ⚠ L280  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L280  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L282  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT category, score FROM {$table}\n
  │  ⚠ L310  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L310  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L312  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COUNT(DISTINCT voter_id) FROM {$table} WHERE target_id = %d"
  │  ⚠ L327  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L327  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L330  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at \t\t\t\t FROM {$table}\n
  │  ⚠ L364  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L364  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L367  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at \t\t\t\t FROM {$table} r\n
  │  ✖ L368  WordPressVIPMinimum.Variables.RestrictedVariables.user_meta__wpdb__users — Usage of users tables is highly discouraged in VIP context
  │  ⚠ L412  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L412  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L414  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT COUNT(*) FROM {$table}\n
  │  ⚠ L421  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L421  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L425  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at \t\t\t\t FROM {$table} r\n
  │  ✖ L426  WordPressVIPMinimum.Variables.RestrictedVariables.user_meta__wpdb__users — Usage of users tables is highly discouraged in VIP context
  │  ⚠ L472  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L472  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L473  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │  ✖ L473  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L474  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │
  ├─ ...\apollo-users\src\Components\UserFields.php
  │  ✖ L208  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │  ⚠ L209  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_POST[$key]
  │  ✖ L209  WordPress.Security.NonceVerification.Missing — Processing form data without nonce verification.
  │
  ├─ ...\apollo-users\src\Plugin.php
  │  ⚠ L163  WordPress.Security.NonceVerification.Recommended — Processing form data without nonce verification.
  │
  ├─ ...\apollo-users\templates\edit-profile.php
  │  ✖ L64   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L104  WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L1241 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'strlen'.
  │  ✖ L1311 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon'.
  │  ✖ L1311 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$filled'.
  │  ✖ L1323 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon'.
  │  ✖ L1323 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$filled'.
  │  ✖ L1336 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$icon'.
  │  ✖ L1336 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$filled'.
  │  ✖ L1419 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post_type
  │  ✖ L1425 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_edit_post_link'.
  │  ✖ L1429 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_the_post_thumbnail_url'.
  │  ✖ L1435 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_trim_words'.
  │
  ├─ ...\apollo-users\templates\parts\matchmaking-widget.php
  │  ✖ L38   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'.
  │
  ├─ ...\apollo-users\templates\parts\profile-edit-form.php
  │  ✖ L90   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'admin_url'.
  │
  ├─ ...\apollo-users\templates\parts\profile-feed.php
  │  ⚠ L14   Squiz.PHP.CommentedOutCode.Found — This comment is 56% valid code; is this commented out code?
  │  ✖ L41   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post_type
  │
  ├─ ...\apollo-users\templates\parts\profile-hero.php
  │  ⚠ L12   Squiz.PHP.CommentedOutCode.Found — This comment is 59% valid code; is this commented out code?
  │
  ├─ ...\apollo-users\templates\parts\profile-sidebar.php
  │  ⚠ L16   Squiz.PHP.CommentedOutCode.Found — This comment is 44% valid code; is this commented out code?
  │
  ├─ ...\apollo-users\templates\parts\user-radar.php
  │  ✖ L17   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $role
  │  ✖ L18   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $orderby
  │
  ├─ ...\apollo-users\templates\profile-login-required.php
  │  ⚠ L17   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L17   WordPress.Security.ValidatedSanitizedInput.InputNotValidated — Detected usage of a possibly undefined superglobal array index: $_SERVER['REQUEST_URI']. Check that the array index exists before using it.
  │  ✖ L27   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_bloginfo'.
  │
  ├─ ...\apollo-users\templates\profile-private.php
  │  ✖ L24   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'get_bloginfo'.
  │
  ├─ ...\apollo-users\templates\single-profile.php
  │  ✖ L48   WordPress.DateTime.RestrictedFunctions.date_date — date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.
  │  ✖ L84   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $term
  │  ✖ L1807 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$badge_html'.
  │  ✖ L1818 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $tag
  │  ✖ L1895 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$i'.
  │  ✖ L1908 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$i'.
  │  ✖ L1922 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$i'.
  │  ✖ L2045 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post
  │  ✖ L2070 WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $post
  │  ✖ L2147 WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$depo_badge_html'.
  │
  ├─ ...\apollo-users\test-radar.php
  │  ✖ L12   WordPress.WP.GlobalVariablesOverride.Prohibited — Overriding WordPress globals is prohibited. Found assignment to $page
  │  ✖ L13   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>apollo_user_page query var: <strong>$page</strong></p>"'.
  │  ⚠ L16   WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — Detected usage of a non-sanitized input variable: $_SERVER['REQUEST_URI']
  │  ✖ L17   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>Current URL: <strong>$current_url</strong></p>"'.
  │  ✖ L21   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>User logged in: <strong>$logged_in</strong></p>"'.
  │  ✖ L25   WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '"<p>Template: <strong>$template</strong></p>"'.
  │
  ├─ ...\apollo-users\uninstall.php
  │  ⚠ L48   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L48   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  └────────────────────────────────────────────

  ┌── apollo-wow [WordPress] ──
  │   Errors: 65  Warnings: 16
  │
  ├─ ...\apollo-wow\apollo-wow.php
  │  ⚠ L29   Universal.NamingConventions.NoReservedKeywordParameterNames.classFound — It is recommended not to use reserved keyword "class" as function parameter name. Found: $class
  │
  ├─ ...\apollo-wow\includes\functions.php
  │  ⚠ L17   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_wow_types', but found: 'apollo/wow/types'.
  │  ✖ L47   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$user_id" missing
  │  ✖ L47   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_type" missing
  │  ✖ L47   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_id" missing
  │  ✖ L47   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$reaction_type" missing
  │  ⚠ L59   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L59   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L61   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT IGNORE INTO {$table} (user_id, object_type, object_id, reaction_type, created_at) VALUES (%d, %s, %d, %s, %s)"
  │  ✖ L71   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L72   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L76   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_wow_added', but found: 'apollo/wow/added'.
  │  ✖ L82   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$user_id" missing
  │  ✖ L82   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_type" missing
  │  ✖ L82   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_id" missing
  │  ✖ L82   Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$reaction_type" missing
  │  ⚠ L89   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L89   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L100  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L103  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_wow_removed', but found: 'apollo/wow/removed'.
  │  ✖ L109  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$user_id" missing
  │  ✖ L109  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_type" missing
  │  ✖ L109  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_id" missing
  │  ✖ L109  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$reaction_type" missing
  │  ⚠ L116  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L116  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L118  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT id FROM {$table} WHERE user_id = %d AND object_type = %s AND object_id = %d AND reaction_type = %s"
  │  ✖ L141  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_type" missing
  │  ✖ L141  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_id" missing
  │  ⚠ L148  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L148  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L150  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT reaction_type, COUNT(*) as count FROM {$table} WHERE object_type = %s AND object_id = %d GROUP BY reaction_type"
  │  ✖ L170  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_type" missing
  │  ✖ L170  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_id" missing
  │  ✖ L175  Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │  ✖ L182  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$user_id" missing
  │  ✖ L182  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_type" missing
  │  ✖ L182  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_id" missing
  │  ⚠ L189  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L189  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L191  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT reaction_type FROM {$table} WHERE user_id = %d AND object_type = %s AND object_id = %d"
  │  ✖ L196  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │  ✖ L199  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_type" missing
  │  ✖ L199  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$object_id" missing
  │
  ├─ ...\apollo-wow\src\Activation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected activation.php, but found Activation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-activation.php, but found Activation.php.
  │  ✖ L1    Squiz.Commenting.FileComment.Missing — Missing file doc comment
  │  ✖ L9    Squiz.Commenting.ClassComment.Missing — Missing doc comment for class Activation
  │  ✖ L10   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function activate()
  │  ✖ L12   Squiz.Commenting.InlineComment.InvalidEndChar — Inline comments must end in full-stops, exclamation marks, or question marks
  │
  ├─ ...\apollo-wow\src\Deactivation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected deactivation.php, but found Deactivation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-deactivation.php, but found Deactivation.php.
  │  ✖ L1    Squiz.Commenting.FileComment.Missing — Missing file doc comment
  │  ✖ L9    Squiz.Commenting.ClassComment.Missing — Missing doc comment for class Deactivation
  │  ✖ L10   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function deactivate()
  │
  ├─ ...\apollo-wow\src\Plugin.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected plugin.php, but found Plugin.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-plugin.php, but found Plugin.php.
  │  ✖ L24   Squiz.Commenting.ClassComment.Missing — Missing doc comment for class Plugin
  │  ✖ L26   Squiz.Commenting.VariableComment.Missing — Missing member variable doc comment
  │  ✖ L28   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function instance()
  │  ✖ L29   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L35   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function __construct()
  │  ✖ L41   Squiz.Commenting.FunctionComment.WrongStyle — You must use "/**" style comments for a function comment
  │  ✖ L95   Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function rest_toggle_wow()
  │  ✖ L110  Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function rest_get_wows()
  │  ✖ L126  Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function rest_remove_wow()
  │  ✖ L134  Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function rest_get_types()
  │  ✖ L138  Squiz.Commenting.FunctionComment.Missing — Missing doc comment for function rest_get_chart()
  │  ✖ L145  Squiz.Commenting.FunctionComment.WrongStyle — You must use "/**" style comments for a function comment
  │  ✖ L150  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$atts" missing
  │  ✖ L168  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$post_id'.
  │  ⚠ L171  WordPress.PHP.StrictInArray.MissingTrueStrict — Not using strict comparison for in_array; supply true for $strict argument.
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$info['emoji']'.
  │  ✖ L176  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$count'.
  │  ✖ L208  Squiz.Commenting.FunctionComment.MissingParamTag — Doc comment for parameter "$atts" missing
  │  ✖ L226  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$data['total']'.
  │  ✖ L235  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$item['emoji']'.
  │  ✖ L237  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$pct'.
  │  ✖ L239  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$item['count']'.
  │  ✖ L239  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$pct'.
  └────────────────────────────────────────────

  ┌── apollo-wow [WordPress-Extra] ──
  │   Errors: 22  Warnings: 6
  │
  ├─ ...\apollo-wow\apollo-wow.php
  │  ⚠ L29   Universal.NamingConventions.NoReservedKeywordParameterNames.classFound — It is recommended not to use reserved keyword "class" as function parameter name. Found: $class
  │
  ├─ ...\apollo-wow\includes\functions.php
  │  ⚠ L17   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_wow_types', but found: 'apollo/wow/types'.
  │  ✖ L61   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT IGNORE INTO {$table} (user_id, object_type, object_id, reaction_type, created_at) VALUES (%d, %s, %d, %s, %s)"
  │  ✖ L72   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L76   WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_wow_added', but found: 'apollo/wow/added'.
  │  ✖ L100  WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ⚠ L103  WordPress.NamingConventions.ValidHookName.UseUnderscores — Words in hook names should be separated using underscores. Expected: 'apollo_wow_removed', but found: 'apollo/wow/removed'.
  │  ✖ L118  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT id FROM {$table} WHERE user_id = %d AND object_type = %s AND object_id = %d AND reaction_type = %s"
  │  ✖ L150  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT reaction_type, COUNT(*) as count FROM {$table} WHERE object_type = %s AND object_id = %d GROUP BY reaction_type"
  │  ✖ L191  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT reaction_type FROM {$table} WHERE user_id = %d AND object_type = %s AND object_id = %d"
  │  ✖ L196  Universal.Operators.DisallowShortTernary.Found — Using short ternaries is not allowed as they are rarely used correctly
  │
  ├─ ...\apollo-wow\src\Activation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected activation.php, but found Activation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-activation.php, but found Activation.php.
  │
  ├─ ...\apollo-wow\src\Deactivation.php
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected deactivation.php, but found Deactivation.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-deactivation.php, but found Deactivation.php.
  │
  ├─ ...\apollo-wow\src\Plugin.php
  │  ⚠ L1    Internal.LineEndings.Mixed — File has mixed line endings; this may cause incorrect results
  │  ✖ L1    WordPress.Files.FileName.NotHyphenatedLowercase — Filenames should be all lowercase with hyphens as word separators. Expected plugin.php, but found Plugin.php.
  │  ✖ L1    WordPress.Files.FileName.InvalidClassFileName — Class file names should be based on the class name with "class-" prepended. Expected class-plugin.php, but found Plugin.php.
  │  ✖ L29   WordPress.PHP.YodaConditions.NotYoda — Use Yoda Condition checks, you must.
  │  ✖ L168  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$post_id'.
  │  ⚠ L171  WordPress.PHP.StrictInArray.MissingTrueStrict — Not using strict comparison for in_array; supply true for $strict argument.
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$info['emoji']'.
  │  ✖ L176  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$count'.
  │  ✖ L226  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$data['total']'.
  │  ✖ L235  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$item['emoji']'.
  │  ✖ L237  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$pct'.
  │  ✖ L239  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$item['count']'.
  │  ✖ L239  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$pct'.
  └────────────────────────────────────────────

  ┌── apollo-wow [WordPress-VIP-Go] ──
  │   Errors: 12  Warnings: 10
  │
  ├─ ...\apollo-wow\includes\functions.php
  │  ⚠ L59   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L59   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L61   WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "INSERT IGNORE INTO {$table} (user_id, object_type, object_id, reaction_type, created_at) VALUES (%d, %s, %d, %s, %s)"
  │  ⚠ L89   WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L89   WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ⚠ L116  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L116  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L118  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT id FROM {$table} WHERE user_id = %d AND object_type = %s AND object_id = %d AND reaction_type = %s"
  │  ⚠ L148  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L148  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L150  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT reaction_type, COUNT(*) as count FROM {$table} WHERE object_type = %s AND object_id = %d GROUP BY reaction_type"
  │  ⚠ L189  WordPress.DB.DirectDatabaseQuery.DirectQuery — Use of a direct database call is discouraged.
  │  ⚠ L189  WordPress.DB.DirectDatabaseQuery.NoCaching — Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
  │  ✖ L191  WordPress.DB.PreparedSQL.InterpolatedNotPrepared — Use placeholders and $wpdb->prepare(); found interpolated variable {$table} at "SELECT reaction_type FROM {$table} WHERE user_id = %d AND object_type = %s AND object_id = %d"
  │
  ├─ ...\apollo-wow\src\Plugin.php
  │  ✖ L168  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$post_id'.
  │  ✖ L175  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$info['emoji']'.
  │  ✖ L176  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$count'.
  │  ✖ L226  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$data['total']'.
  │  ✖ L235  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$item['emoji']'.
  │  ✖ L237  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$pct'.
  │  ✖ L239  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$item['count']'.
  │  ✖ L239  WordPress.Security.EscapeOutput.OutputNotEscaped — All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$pct'.
  └────────────────────────────────────────────

  Output Files: