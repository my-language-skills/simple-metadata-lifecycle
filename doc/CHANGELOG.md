# Changelog
## 1.2
* Plugin
  * **Changed** network options from option table to sitemeta table
  * **Changed** Make the plugin work only for Creative Works type
  * **Changed** Microdata to json-ld
  * **Fixed** warning in setting page of Life Cycle

* Chapter
  * **Remove** bookEdition


## 1.1
* **Additions**
		* Disable and delete data of fields in database
		* Create radio button for settings of fields

* **Remove**
    *  Auto update from github

## 1.0
* **Additions**

  * **Properties**
    * **Life Cycle**
      * Version

  * **Administration**
   * **Settings**
      * **Network settings** (uses Simple-Metadata network settings page)
        * Post types active for Life Cycle Metadata (show/not show metatags in web-pages code and metaboxes to fill in information)
        * Overwriting of properties (Freeze)
        * Seeding properties values (Share, affects only if desired field is empty in active post level)
        * Language Education Mode (on/off)
      *Network settings overwrite all site settings and block ability to change them!*
      * **Site Settings**
        * **Simple Metadata Settings Page**
          * Post types active for Life Cycle Metadata (show/not show metatags in web-pages code and metaboxes to fill in information)
        * **Educational Metadata Settings** (subpage under Simple Metadata Settings)  
          * Overwriting of properties (Freeze)
          * Seeding properties values (Share, affects only if desired field is empty in active post level)
      *If overwriting for some property is activated, seeding is also marked active in order to avoid misunderstanding for user*
        * **Site Meta**
          This is a place where you enter metadata infromation, which will be shown in front-page of a site.
      *Overwriting and seeding applies information, stored in Site-Meta/Book Info to all active post types*
