# WordPress Drama Levels Plugin
The purpose of this plugin is to “calculate” the percentage of drama/strife in comments and posts based on the tone of the content. The plugin can support multiple tone analyzers controlled by a custom WordPress setting. The drama level is saved as custom meta tags for the comments/posts.  All the tone analyzers are silly fictional classes to demonstrate the functionality of the framework. My inspiration for this plugin is base on the general negativity I see in the WordPress blogs I frequent

## Features
- Admin Post grid shows the drama level per post and is sortable.
- The average comment drama level is shown per Post.
- Admin comment grid shows drama level per comment and is sortable.
- The drama provider framework is designed to easily add new providers. A new provider adaptor should implement the IDrama_Provider interface and be placed in the provider folder. It will automatically be selectable as the default provider via the setting interface.
- If the selected drama provider is unavailable for any reason, the system will fall back to the disabled provider.

