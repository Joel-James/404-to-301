<template>
    <tr id="post-8" class="iedit author-self level-0 post-8 type-page status-publish hentry">
        <th scope="row" class="check-column">
            <label class="screen-reader-text" for="cb-select-17">Select Home</label>
            <input id="cb-select-17" type="checkbox" name="post[]" value="17">
            <div class="locked-indicator">
                <span class="locked-indicator-icon" aria-hidden="true"></span>
                <span class="screen-reader-text">“Home” is locked</span>
            </div>
        </th>
        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
            Hello World
        </td>
        <td class="author column-author" data-colname="Author">
            <a href="edit.php?post_type=page&amp;author=1">joel</a>
        </td>
        <td class="comments column-comments" data-colname="Comments">
            <div class="post-com-count-wrapper">
                <span aria-hidden="true">—</span>
                <span class="screen-reader-text">No comments</span>
                <span class="post-com-count post-com-count-pending post-com-count-no-pending">
                                            <span class="comment-count comment-count-no-pending" aria-hidden="true">0</span>
                                            <span class="screen-reader-text">No comments</span>
                                        </span>
            </div>
        </td>
        <td class="date column-date" data-colname="Date">Published<br>
            <abbr title="2019/07/19 6:06:22 am">2019/07/19</abbr>
        </td>
    </tr>
</template>

<script>
export default {

    name: 'Row',

    data () {
        return {

        };
    }
};
</script>