# Testing Checklist — Elementor ACF Group Fields 2.0

Use a staging site with **Elementor Pro**, **ACF PRO** (recommended for Repeater / Flexible / Clone), and a sample post type.

Mark each item as you verify it.

---

## Environment

- [ ] WordPress ≥ 5.8
- [ ] PHP ≥ 8.0
- [ ] Elementor + Elementor Pro active
- [ ] ACF (PRO preferred) active
- [ ] Plugin activates without admin fatals
- [ ] No PHP notices on a fresh page load with Elementor canvas open

---

## Backwards compatibility

- [ ] Existing `acf_group_tag` bindings still resolve text values
- [ ] Existing `acf_group_image_tag` bindings still resolve images
- [ ] Existing `acf_group_gallery_tag` bindings still populate Gallery widgets
- [ ] Legacy file `elementor-extension-acf-group-tag.php` loads the plugin once (no double tags)

---

## Nested Groups

Create:

```
Level1 (Group)
 └─ Level2 (Group)
     └─ Level3 (Group)
         └─ Level4 (Group)
             └─ Deep Text (Text)
```

- [ ] Option appears roughly as `Level1 → Level2 → Level3 → Level4 → Deep Text`
- [ ] Dynamic tag outputs the saved value on the front end
- [ ] Works in Elementor editor preview
- [ ] Works with ACF Options Page location (if used)

---

## URL field

```
Hero (Group) → CTA URL (URL)
```

- [ ] Field appears under **ACF Nested URL / Link**
- [ ] Also usable as text where appropriate
- [ ] Returns the raw URL string
- [ ] Works inside Button / Link controls

---

## Link field

```
Hero (Group) → Button (Group) → Link (Link)
```

- [ ] `… → Link → URL` returns URL
- [ ] `… → Link → Title` returns title
- [ ] `… → Link → Target` returns target (`_blank`, etc.)

---

## Image field

```
Card (Group) → Photo (Image)
```

- [ ] Image widget accepts **ACF Nested Image**
- [ ] Properties available: ID, URL, Alt, Caption, Description, Width, Height, Mime Type, Filename
- [ ] Fallback media control works when empty
- [ ] Supports ACF return formats: Array, URL, ID

---

## File field

```
Resources (Group) → Attachment (File)
```

- [ ] **ACF Nested File / Media** returns URL/ID for media controls
- [ ] Text properties: Title, Filename, Size, Mime, Extension, ID

---

## Gallery

```
Portfolio (Group) → Shots (Gallery)
```

- [ ] Elementor Gallery / Image Carousel accepts **ACF Nested Gallery**
- [ ] Front end renders all attachment IDs
- [ ] Empty gallery returns empty array (no fatals)

---

## Nested Repeaters

```
Team (Repeater)
 └─ Member (Group)
      ├─ Name (Text)
      └─ Roles (Repeater)
           └─ Role Title (Text)
```

- [ ] Fields appear in the tag picker
- [ ] Row `1` returns first Team row values
- [ ] Row `2` returns second Team row values
- [ ] Nested repeater values resolve with row control / path heuristics
- [ ] Group inside repeater works
- [ ] Repeater inside group works

---

## Group → Repeater → Group

```
Page (Group)
 └─ Items (Repeater)
      └─ Meta (Group)
           └─ Label (Text)
```

- [ ] Discoverable in picker
- [ ] Correct value for selected row

---

## Flexible Content

```
Sections (Flexible Content)
 ├─ Layout: Hero
 │    └─ Headline (Text)
 └─ Layout: Gallery
      └─ Images (Gallery)
```

- [ ] Hero → Headline appears with layout label in path
- [ ] Gallery layout images work with Gallery tag
- [ ] Row index selects the Nth flexible row when needed

---

## Clone

- [ ] Cloned fields appear once (no duplicate keys)
- [ ] `prefix_name` clones keep prefixed meta keys
- [ ] Nested clone of a group expands correctly

---

## Post Object

- [ ] ID, Title, Permalink, Slug, Excerpt, Featured Image URL, Author, Date
- [ ] Page Link URL-only values still work for Permalink / URL tag

---

## Relationship

- [ ] Titles (comma-separated)
- [ ] URLs (comma-separated)
- [ ] IDs (comma-separated)

---

## Taxonomy

- [ ] Name, Slug, Description, Archive URL, ID
- [ ] Works with single and multi-term return formats (first term for scalar props)

---

## User

- [ ] Display Name, Username, Email, Website, Avatar URL, ID
- [ ] Avatar usable via image tag where exposed

---

## Google Map

- [ ] Address (default)
- [ ] Latitude, Longitude, Zoom

---

## Choice / misc scalars

- [ ] Select (single + multiple) shows labels
- [ ] Checkbox shows labels
- [ ] Radio / Button Group show labels
- [ ] True/False → `1` / `0`
- [ ] WYSIWYG renders allowed HTML via `wp_kses_post`
- [ ] Color Picker returns hex/string
- [ ] Date / DateTime / Time return stored strings
- [ ] oEmbed returns embed HTML or stored value without fatals

---

## Performance / safety

- [ ] Opening Elementor editor twice does not re-register duplicate tags
- [ ] Saving an ACF field group refreshes discovered options (cache flush)
- [ ] Extremely deep nesting (8+ groups) still works
- [ ] Circular clone definitions do not infinite-loop (walker visit guard)

---

## Regression smoke

- [ ] Deactivate plugin → pages load
- [ ] Reactivate → tags available again
- [ ] Switch theme → tags still resolve on a simple Elementor template
