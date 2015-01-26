# Fieldchooser

## Choose among several ExpressionEngine fields on the Publish page

This is a poor man's flexible field type.  Sometimes you have a content area that might be two different things.  For me, a frequent use case is a content block that might sometimes be a locally hosted image, or sometimes be e.g., a Vimeo video embed.  These are two different fieldtypes (a File and maybe a Text URL), and their content is mutually exclusive, so it's confusing to the user to see them both on the Publish page.

What Fieldchooser does is let the user choose which fieldtype they'd like to use for a particular channel entry.  If they want this entry to use a Vimeo embed, they'd select the "Vimeo URL" field and Fieldchooser will hide (i.e., with javascript, on the Publish page) the corresponding "Image" File fieldtype.  On the frontend, Filechooser will simply output the name of the selected field, which is useful in a conditional render block:

```
{if "{media_chooser}" == "vimeo_url"}
  Your video URL is: {vimeo_url}
{if:else}
  Your image URL is: {image}
{/if}
```

When adding a Fieldchoooser field to a channel fieldset, you choose which fields you want to present to the user (in the above example, you'd pick out "Vimeo URL" and "Image" from a multiselect box). Obviously, you need to create these fields before you try to set up a Fieldchooser.