<?php

/**
 * HumHub
 * Copyright © 2014 The HumHub Project
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 */
return [
    ['id' => 1, 'element_id' => 1, 'dyn_attributes' => json_encode(['content' => '<p>Default</p>'])],
    ['id' => 2, 'element_id' => 5, 'dyn_attributes' => json_encode(['content' => '<p>ContainerText</p>']), 'template_instance_id' => 3],

    ['id' => 3, 'element_id' => 6, 'dyn_attributes' => json_encode(['content' => '<p>ContainerText</p>']), 'template_instance_id' => 4],
    ['id' => 4, 'element_id' => 6, 'dyn_attributes' => json_encode(['content' => '<p>ContainerText</p>']), 'template_instance_id' => 5],
    ['id' => 5, 'element_id' => 6, 'dyn_attributes' => json_encode(['content' => '<p>ContainerText</p>']), 'template_instance_id' => 6],
];
