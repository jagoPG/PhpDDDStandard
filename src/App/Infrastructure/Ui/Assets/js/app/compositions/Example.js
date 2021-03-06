/*
 * This file is part of the Php DDD Standard project.
 *
 * Copyright (c) 2017 LIN3S <info@lin3s.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */

'use strict';

import {onDomReady, onDomLoaded} from 'lin3s-event-bus';
// import $ from 'jquery';

const onReady = () => {
  console.log('DOM is ready!');
};

const onLoaded = () => {
  console.log('window is loaded!');
};

onDomReady(onReady);
onDomLoaded(onLoaded);
