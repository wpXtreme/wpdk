<?php

/**
 * Type of bar.
 *
 * @class              WPDKUIProgressPlacement
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-23
 * @version            1.0.0
 * @since              1.5.0
 */
class WPDKUIProgressBarType {

  const BASIC   = '';
  const SUCCESS = '-success';
  const INFO    = '-info';
  const WARNING = '-warning';
  const DANGER  = '-danger';
}

/**
 * @class              WPDKUIProgress
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-10-31
 * @version            1.0.0
 * @since              1.7.1
 */
class WPDKUIProgress extends WPDKHTMLTag {

  /**
   * Progress (container) id.
   *
   * @var string $id
   */
  public $id = '';

  /**
   * List of WPDKUIProgressBar instances.
   *
   * @var array $bars
   */
  public $bars = array();

  /**
   * Width (px or %) of progress container.
   *
   * @var string $width
   */
  public $width = '';

  /**
   * Return an instance of WPDKUIProgress class
   *
   * @brief Construct
   *
   * @param string                      $id                 ID attribute for main container.
   * @param int|array|WPDKUIProgressBar $percentage_or_bars Optional. Numeric percentage, array of WPDKUIProgressBar or
   *                                                        an instance of WPDKUIProgressBar.
   * @param string                      $progress_bar_type  Optional. Type of progress. This params is ignored if
   *                                                        `$percentage_or_bars` is ans array of WPDKUIProgressBar
   *
   * @return WPDKUIProgress
   */
  public static function init( $id, $percentage_or_bars = 0, $progress_bar_type = WPDKUIProgressBarType::BASIC )
  {
    return new self( $id, $percentage_or_bars, $progress_bar_type );
  }

  /**
   * Create an instance of WPDKUIProgress class.
   *
   * @brief Construct
   *
   * @param string                      $id                 ID attribute for main container.
   * @param int|array|WPDKUIProgressBar $percentage_or_bars Optional. Numeric percentage, array of WPDKUIProgressBar or
   *                                                        an instance of WPDKUIProgressBar.
   * @param string                      $progress_bar_type  Optional. Type of progress. This params is ignored if
   *                                                        `$percentage_or_bars` is ans array of WPDKUIProgressBar
   *
   * @return WPDKUIProgress
   */
  public function __construct( $id, $percentage_or_bars = 0, $progress_bar_type = WPDKUIProgressBarType::BASIC )
  {
    $this->id = sanitize_title( $id );

    // Create a single progress bar
    if( is_numeric( $percentage_or_bars ) ) {
      $this->bars[ ] = new WPDKUIProgressBar( $this->id . '-bar', $percentage_or_bars, $progress_bar_type );
    }
    // Instance of WPDKUIProgressBar
    elseif( $percentage_or_bars instanceof WPDKUIProgressBar ) {
      $this->bars[] = $percentage_or_bars;
    }
    // Add your progress bar list
    elseif( is_array( $percentage_or_bars ) ) {
      $this->bars = $percentage_or_bars;
    }
  }

  /**
   * Return the HTML markup of Progress
   *
   * @brief HTML makrup
   *
   * @return string
   */
  public function html()
  {
    // Width
    $width = empty( $this->width ) ? '' : sprintf( 'style="width:%s"', $this->width );

    WPDKHTML::startCompress(); ?>

    <div id="<?php echo $this->id ?>"
      <?php echo $width ?>
         class="wpdk-progress">
      <?php foreach( $this->bars as $bar ) : ?>
        <?php $bar->display() ?>
      <?php endforeach; ?>
    </div>

    <?php
    return WPDKHTML::endCompress();
  }

}

/**
 * @class              WPDKUIProgress
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-10-31
 * @version            1.0.0
 * @since              1.7.1
 */
class WPDKUIProgressBar extends WPDKHTMLTag {

  /**
   * Progress (container) id.
   *
   * @var string $id
   */
  public $id = '';

  /**
   * Percentage
   *
   * @var int $percentage
   */
  public $percentage = 0;

  /**
   * Display the percentage inner progress bar.
   *
   * @var bool $displayPercentage
   */
  public $displayPercentage = true;

  /**
   * Type (style) of bar
   *
   * @var string
   */
  public $type = WPDKUIProgressBarType::BASIC;

  /**
   * Display an animated progress bar.
   * If this property is TRUE also $stripe is TRUE.
   *
   * @var bool $animated
   */
  public $animated = false;

  /**
   * Display a striped progress bar.
   *
   * @var bool $striped
   */
  public $striped = false;

  /**
   * The label used instead the percentage.
   *
   * @var string $label
   */
  public $label = '';

  /**
   * Create an instance of WPDKUIProgress class
   *
   * @param string $id        ID attribute
   * @param string $title     Optional. Title of popover
   * @param string $content   Optional. Content of popover
   * @param string $placement Optional. Default WPDKUIProgressPlacement::RIGHT
   *
   * @return WPDKUIProgress
   */
  public function __construct( $id, $percentage = 0, $progress_bar_type = WPDKUIProgressBarType::BASIC )
  {
    $this->id         = sanitize_title( $id );
    $this->percentage = min( absint( $percentage ), 100 );
    $this->type       = $progress_bar_type;
  }

  /**
   * Return the HTML markup of single Progress bar
   *
   * @brief HTML markup
   *
   * @return string
   */
  public function html()
  {

    // Type
    $type = empty( $this->type ) ? '' : 'wpdk-progress-bar' . $this->type;

    // Animated
    $animated = empty( $this->animated ) ? '' : 'active';

    // Striped
    $striped = ( empty( $this->striped ) && !$animated ) ? '' : 'wpdk-progress-bar-striped';

    WPDKHTML::startCompress(); ?>

    <div class="wpdk-progress-bar <?php echo $type ?> <?php echo $animated ?> <?php echo $striped ?>"
         id="<?php echo $this->id ?>"
         data-label="<?php echo $this->label ?>"
         role="wpdk-progressbar"
         aria-valuenow="<?php echo $this->percentage ?>"
         aria-valuemin="0"
         aria-valuemax="100"
         style="width: <?php echo $this->percentage ?>%">

        <span class="sr-only">
          <?php if( $this->displayPercentage ) : ?>
            <?php echo empty( $this->label ) ? $this->percentage . '%' : $this->label ?>
          <?php endif; ?>
        </span>

    </div>

    <?php
    return WPDKHTML::endCompress();
  }

}


