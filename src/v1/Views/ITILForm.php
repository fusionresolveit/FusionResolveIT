<div class="ui <?=$color?> dividing header">
  <i class="big <?php echo $icon; ?> icon"></i>
  <div class="content">
    <?=$name?>
    | 
    <?php foreach ($fields as $item): ?>
      <?php if ($item['name'] == 'status'): ?>
        <span class="ui <?=$item['values'][$item['value']]['color']?> text">
          <i class="<?=$item['values'][$item['value']]['icon']?> icon"></i>
          <?=$item['values'][$item['value']]['title']?>
        </span>
      <?php endif ?>
    <?php endforeach ?>
    <div class="sub header">
      <?php if (count($fields) > 0): ?>
        <?=current($fields)['value']?>
      <?php endif ?>
    </div>
  </div>
</div>

<?php if (count($relatedPages) > 0): ?>
  <div class="ui equal width labeled icon menu">
    <?php foreach ($relatedPages as $item): ?>
      <a class="item" href="<?=$item['link']?>">
        <i class="icon blue <?=$item['icon']?>"></i>
        <?=$item['title']?>
        <!-- <div class="floating ui blue label">22</div> -->
      </a>
    <?php endforeach ?>
  </div>
<?php endif ?>

  <div class="ui internally celled grid">
    <div class="row">
      <div class="twelve wide column">
        <form class="ui form <?=$color?> segment">
          <div class="field">
            <label>Description</label>
            <div class="itilcontent">
              <?=$content?>
            </div>
          </div>
        </form>
        <!-- ?php foreach ($fields as $item): ?>
          ?php if ($item['name'] == 'content'): ?>
            <div id="editor?php echo $item['name'] ?>">?=$item['value']?></div>
            <textarea rows="3" name="?=$item['name']?>" ?php if (isset($item['readonly'])){ ?>readonly="?=$item['readonly']?>"?php } ?>  style="display: none">?=$item['value']?></textarea>
<script type="text/javascript">
editor?php echo $item['name'] ?> = new toastui.Editor({
  el: document.querySelector('#editor?php echo $item['name'] ?>'),
  initialEditType: 'markdown',
  previewStyle: 'vertical',
  initialEditType: 'wysiwyg',
  events: {
    blur: () => {
      document.querySelector(`textarea[name='?=$item['name']?>']`).value = editor?php echo $item['name'] ?>.getMarkdown();
    },
  },
});
</script>
          ?php endif ?>
        ?php endforeach ?> -->

        <!-- Tabs: followup, solutions -->
        <div class="ui secondary menu">
          <a class="item active" data-tab="feeds">Feeds</a>
          <a class="yellow item" data-tab="followup">Followup</a>
          <a class="blue item" data-tab="solution">Solution</a>
        </div>
        <div class="ui tab segment" hidden data-tab="feeds">
        </div>
        <div class="ui active tab yellow segment" data-tab="followup">
          <form method="post" action="/gsit/followups" class="ui form">
            <div class="ui grid">
              <div class="eleven wide column">
                <div id="editorfollowup"></div>
                <textarea rows="3" name="followup" style="display: none"></textarea>
<script type="text/javascript">
editorfollowup = new toastui.Editor({
  el: document.querySelector('#editorfollowup'),
  initialEditType: 'markdown',
  previewStyle: 'vertical',
  initialEditType: 'wysiwyg',
  height: '200px',
  minHeight: '200px',
  events: {
    blur: () => {
      document.querySelector(`textarea[name='followup']`).value = editorfollowup.getMarkdown();
    },
  },
});
</script>
              </div>
              <div class="five wide column">
                <div class="field">
                  <label>Template</label>
                  <select name="template">
                  </select>
                </div>

                <div class="field">
                  <label>Privé</label>
                  <div class="ui fitted mini toggle checkbox">
                    <input type="checkbox" name="private"> <label></label>
                  </div>
                </div>

                <div class="field">
                  <label>Source du suivi</label>
                  <select name="source">
                  </select>
                </div>
              </div>
            </div>
            <div class="ui grid">
              <div class="five wide column">
                <div class="field">
                  <label>Catégorie</label>
                  <select name="category">
                  </select>
                </div>
                <div class="field">
                  <label>Status</label>
                  <select name="status">
                  </select>
                </div>
              </div>
              <div class="five wide column">
                <div class="field" style="width: 100px;margin-left: auto;margin-right: auto;">
                  <label>Durée</label>
                  <div class="ui right labeled input">
                    <input type="number" name="time"  min="0">
                    <div class="ui grey inverted label">
                      <div class="ui labeled dropdown button">
                        <input type="hidden" name="timetype">
                        <i class="dropdown icon"></i>
                        <div class="default text">secondes</div>
                        <div class="menu">
                          <div class="item" data-value="seconds">secondes</div>
                          <div class="item" data-value="minutes">minutes</div>
                          <div class="item" data-value="hours">heures</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="six wide column">
                <div class="field">
                  <label>Utilisateur</label>
                  <select name="user">
                  </select>
                </div>
                <div class="field">
                  <label>Groupe</label>
                  <select name="group">
                  </select>
                </div>
              </div>
            </div>
            <div style="text-align: center;">
              <button class="ui primary button" type="submit">Add followup</button>
            </div>
          </form>
        </div>
        <div class="ui tab segment" data-tab="solution">
          TODO
        </div>

        <!-- Feeds -->
        <div class="ui connected feed">
          <?php foreach ($feeds as $feed): ?>
            <div class="event">
              <div class="label">
                <?php if ($feed['type'] == 'event'): ?>
                  <i class="circular colored inverted exchange alternate brown icon"></i>
                <?php elseif ($feed['usertype'] == 'tech'): ?>
                  <i class="circular colored inverted headset olive icon"></i>
                <?php else: ?>
                  <i class="circular colored inverted user blue icon"></i>
                <?php endif ?>
                <!-- <img src="/images/avatar/small/elliot.jpg"> -->
              </div>
              <div class="content">
                <div class="summary">
                  <a class="user">
                    <?=$feed['user']?>
                  </a> <?=$feed['summary']?>
                  <div class="date">
                    <?=$feed['date']?>
                  </div>
                </div>
                <?php if (!is_null($feed['content'])): ?>
                  <div class="extra text">
                    <?=$feed['content']?>
                  </div>
                <?php endif ?>
                <?php if (!is_null($feed['time'])): ?>
                  <div class="meta">
                    <a class="like">
                      Temps passé: <?=$feed['time']?> secondes
                    </a>
                  </div>
                <?php endif ?>
              </div>
            </div>
          <?php endforeach ?>
        </div>
      </div>
      <div class="four wide column">
        <form method="post" class="ui form">
          <button class="ui primary button right floated" type="submit">Save</button>
        <?php foreach ($fields as $item): ?>
          <?php if ($item['name'] == 'content') { continue; } ?>
          <div class="field">
            <label><?=$item['title']?></label>
            <?php if ($item['type'] == 'input'): ?>
              <input type="text" name="<?=$item['name']?>" <?php if (isset($item['readonly'])){ ?>readonly="<?=$item['readonly']?>"<?php } ?> value="<?=$item['value']?>">
            <?php endif ?>

            <?php if ($item['type'] == 'dropdown'): ?>
              <div class="ui selection dropdown">
                <input type="hidden" name="<?=$item['name']?>" <?php if (isset($item['readonly'])){ ?>readonly="<?=$item['readonly']?>"<?php } ?>  value="<?=$item['value']?>">
                <i class="dropdown icon"></i>
                <div class="default text">Select value...</div>
                <div class="menu">
                  <?php foreach ($item['values'] as $key=>$val): ?>
                    <div class="item" data-value="<?=$key?>">
                      <?php if (isset($val['icon']) && !empty($val['icon'])): ?>
                        <i class="<?=$val['color']?> <?=$val['icon']?> icon"></i>
                      <?php endif ?>
                      <?=$val['title']?>
                  </div>
                  <?php endforeach ?>
                </div>
              </div>
            <?php endif ?>

            <?php if ($item['type'] == 'dropdown_remote'): ?>
              <?php if (isset($item['readonly'])){ ?>
                <?php if ($item['value'] > 0): ?>
                  <div class="text"><?=$item['valuename']?></div>
                <?php endif ?>
              <?php } ?>

              <?php if (!isset($item['readonly'])){ ?>
              <div
                class="ui <?php if (isset($item['multiple'])) { echo 'multiple ';} ?>selection dropdown remotedropdown"
                data-url="http://127.0.0.1/gsit/dropdown"
                data-itemtype="<?=$item['itemtype']?>"
              >
                <input type="hidden" name="<?=$item['name']?>" <?php if (isset($item['readonly'])){ ?>readonly="<?=$item['readonly']?>"<?php } ?>  value="<?=$item['value']?>">
                <i class="dropdown icon"></i>
                <?php if ($item['value'] == 0): ?>
                  <div class="default text">Select value...</div>
                <?php endif ?>
                <?php if ($item['value'] > 0): ?>
                  <div class="text"><?=$item['valuename']?></div>
                <?php endif ?>
                <div class="menu">
                  <?php if (isset($item['multiple']) && $item['value'] != ''): ?>
                    <?php for ($i=0; $i<count(explode(',', $item['value'])); $i++): ?>
                      <div class="item" data-value="<?=explode(',', $item['value'])[$i]?>"><?=explode(',', $item['valuename'])[$i]?></div>
                    <?php endfor ?>
                  <?php endif ?>
                </div>
              </div>
              <?php } ?>
            <?php endif ?>

            <?php if ($item['type'] == 'textarea'): ?>
              <div id="editor<?php echo $item['name'] ?>"><?=$item['value']?></div>
              <textarea rows="3" name="<?=$item['name']?>" <?php if (isset($item['readonly'])){ ?>readonly="<?=$item['readonly']?>"<?php } ?>  style="display: none"><?=$item['value']?></textarea>
<script type="text/javascript">
editor<?php echo $item['name'] ?> = new toastui.Editor({
  el: document.querySelector('#editor<?php echo $item['name'] ?>'),
  initialEditType: 'markdown',
  previewStyle: 'vertical',
  initialEditType: 'wysiwyg',
  events: {
    blur: () => {
      document.querySelector(`textarea[name='<?=$item['name']?>']`).value = editor<?php echo $item['name'] ?>.getMarkdown();
    },
  },
});
</script>
            <?php endif ?>

            <?php if ($item['type'] == 'boolean'): ?>
              <?php if (isset($item['readonly'])){ ?>
                <?php if ($item['value'] == 1) { ?>Oui<?php } ?>
                <?php if ($item['value'] == 0) { ?>Non<?php } ?>
              <?php } ?>

              <?php if (!isset($item['readonly'])){ ?>
                <select name="<?=$item['name']?>">
                  <option <?php if ($item['value'] == 1) { ?>selected="selected"<?php } ?> value="1">Oui</option>
                  <option <?php if ($item['value'] == 0) { ?>selected="selected"<?php } ?> value="0">Non</option>
                </select>
              <?php } ?>

            <?php endif ?>

            <?php if ($item['type'] == 'date'): ?>
              <?php if (isset($item['readonly'])){ ?>
                <?php echo $item['value'] ?>
              <?php } ?>

              <?php if (!isset($item['readonly'])){ ?>
                <input type="date" name="<?=$item['name']?>" value="<?=$item['value']?>">
              <?php } ?>
            <?php endif ?>

            <?php if ($item['type'] == 'email'): ?>
              <input type="email" name="<?=$item['name']?>" <?php if (isset($item['readonly'])){ ?>readonly="<?=$item['readonly']?>"<?php } ?> value="<?=$item['value']?>">
            <?php endif ?>

            <?php if ($item['type'] == 'datetime'): ?>
              <?php if (isset($item['readonly'])){ ?>
                <?php echo $item['value'] ?>
              <?php } ?>

              <?php if (!isset($item['readonly'])){ ?>
                <input type="text" name="<?=$item['name']?>" value="<?=$item['value']?>">
              <?php } ?>
            <?php endif ?>
          </div>
        <?php endforeach ?>
          <button class="ui primary button right floated" type="submit">Save</button>
        </form>

      </div>
    </div>
  </div>