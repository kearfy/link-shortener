<?php
    use \Library\Objects;

    $obj = new Objects();
    $list = $obj->list('mod_link-shortener_link');
?>

<section class="page-introduction">
    <h1>
        Link shortener
    </h1>
    <p>
        Create new links or delete an existing one.
    </p>
</section>

<section class="new-link">
    <h2>New link</h2>
    <form class="new-short-link">
        <input type="text" name="link" placeholder="<?php echo SITE_LOCATION; ?>YOUR_SHORT_LINK_HERE">
        <input type="text" name="target" placeholder="Target link">
        <button>
            Create link
        </button>
    </form>
</section>

<section class="no-padding transparent existing-links">
    <table>
        <thead>
            <tr>
                <th>
                    Link
                </th>
                <th>
                    Target
                </th>
                <th>
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($list as $link) {
                    $target = $obj->get('mod_link-shortener_link', $link['name'], 'url');
                    ?>
                        <tr link="<?=$link['name']?>">
                            <td selectable><?=$link['name']?></td>
                            <td>
                                <a selectable href="<?=$target?>" target="_blank">
                                    <?=$target?>
                                </a>
                            </td>
                            <td>
                                <a href="#" delete-link="<?=$link['name']?>">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</section>