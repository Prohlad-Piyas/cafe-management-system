<?php
require_once __DIR__ . "/../../controllers/authGuard.php";
requireRole("manager");

require_once __DIR__ . "/../../models/menuModel.php";

$items=getAllMenuItems(false);
$editItem=null;
if(isset($_GET["edit"]))
    {
        $editItem=getMenuItemById((int)$_GET["edit"]);
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="dash dash-wide">
    <div class="dash-inner">
        <h1>Menu</h1>
        <p class="sub">Add, edit, or remove items customers can order.</p>

        <?php dashNav("manager", "menu"); ?>

        <?php if(isset($_GET["menuOk"])) { ?>
            <p class="notice notice-good"><?php showGet("menuOk"); ?></p>
        <?php } ?>

        <?php if(isset($_GET["menuError"])) { ?>
            <p class="notice notice-bad"><?php showGet("menuError"); ?></p>
        <?php } ?>

        <div class="panel" style="max-width:560px;">
            <h2><?php echo $editItem ? "Edit item" : "Add a new item"; ?></h2>

            <form action="../../controllers/menuControls.php" method="post">
                <input type="hidden" name="action" value="<?php echo $editItem ? "update" : "add"; ?>">
                <?php if($editItem) { ?>
                    <input type="hidden" name="itemId" value="<?php echo (int)$editItem["itemId"]; ?>">
                <?php } ?>

                <div class="field">
                    <label for="name">Item name</label>
                    <input type="text" id="name" name="name"
                           value="<?php echo $editItem ? e($editItem["name"]) : ""; ?>">
                    <span class="err"><?php showGet("nameError"); ?></span>
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2"><?php echo $editItem ? e($editItem["description"]) : ""; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="price">Price (&#2547;)</label>
                        <input type="text" id="price" name="price"
                               value="<?php echo $editItem ? e($editItem["price"]) : ""; ?>">
                        <span class="err"><?php showGet("priceError"); ?></span>
                    </div>

                    <div class="field">
                        <label for="category">Category</label>
                        <input type="text" id="category" name="category"
                               value="<?php echo $editItem ? e($editItem["category"]) : ""; ?>"
                               placeholder="Coffee, Tea, Food, Dessert...">
                        <span class="err"><?php showGet("categoryError"); ?></span>
                    </div>
                </div>

                <?php if($editItem) { ?>
                    <div class="check-field">
                        <input type="checkbox" id="available" name="available"
                               <?php echo $editItem["available"] ? "checked" : ""; ?>>
                        <label for="available">Available to order</label>
                    </div>
                <?php } ?>

                <button type="submit" class="btn" style="width:auto;">
                    <?php echo $editItem ? "Save changes" : "Add item"; ?>
                </button>

                <?php if($editItem) { ?>
                    <a class="btn btn-plain btn-inline" href="manageMenu.php">Cancel</a>
                <?php } ?>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($items)==0) { ?>
                    <tr class="muted-row"><td colspan="5">No menu items yet.</td></tr>
                <?php } ?>

                <?php foreach($items as $item) { ?>
                    <tr>
                        <td><?php echo e($item["name"]); ?></td>
                        <td><?php echo e($item["category"]); ?></td>
                        <td>&#2547;<?php echo number_format($item["price"], 2); ?></td>
                        <td>
                            <?php if($item["available"]) { ?>
                                <span class="badge badge-served">Available</span>
                            <?php } else { ?>
                                <span class="badge badge-preparing">Hidden</span>
                            <?php } ?>
                        </td>
                        <td>
                            <a class="btn btn-plain btn-sm btn-inline" href="manageMenu.php?edit=<?php echo (int)$item["itemId"]; ?>">Edit</a>

                            <form class="inline-form" action="../../controllers/menuControls.php" method="post"
                                  onsubmit="return confirm('Remove this item from the menu?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="itemId" value="<?php echo (int)$item["itemId"]; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
