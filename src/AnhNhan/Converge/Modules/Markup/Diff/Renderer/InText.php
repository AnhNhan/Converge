<?php
/**
 * Inline HTML diff generator for PHP DiffLib.
 *
 * PHP version 5
 *
 * Copyright (c) 2009 Chris Boulton <chris.boulton@interspire.com>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  - Neither the name of the Chris Boulton nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package DiffLib
 * @author Chris Boulton <chris.boulton@interspire.com>
 * @copyright (c) 2009 Chris Boulton
 * @license New BSD License http://www.opensource.org/licenses/bsd-license.php
 * @version 1.1
 * @link http://github.com/chrisboulton/php-diff
 */

namespace AnhNhan\Converge\Modules\Markup\Diff\Renderer;

use AnhNhan\Converge as cv;

class InText extends ArrayRenderer2
{
    /**
     * Render a and return diff with changes between the two sequences
     * displayed inline (under each other)
     *
     * @return string The generated inline diff.
     */
    public function render()
    {
        $changes = parent::render();
        $html = '';
        if(empty($changes)) {
            return $html;
        }

        $html .= '<div class="Differences DifferencesInText">';
        foreach($changes as $i => $blocks) {
            // If this is a separate block, we're condensing code so output ...,
            // indicating a significant portion of the code has been collapsed as
            // it is the same
            if($i > 0) {
                $html .= '<div class="Skipped">';
                $html .= '<span>&hellip;</span>';
                $html .= '</div>';
            }

            $fun = function ($class, array $lines)
            {
                $lines = implode("\n", $lines) ?: '&nbsp;';
                $tag = preg_match('/^<(div|p|h\d)>/', $lines) ? 'div' : 'span';
                return cv\ht($tag, cv\safeHtml($lines))->addClass($class);
            };

            foreach($blocks as $change) {
                $html_prefix = '';
                $html_suffix = '';

                // Equal changes should be shown on both sides of the diff
                if($change['tag'] == 'equal') {
                    $html .= $fun('diff-equal', $change['base']['lines']);
                }
                // Added lines only on the right side
                else if($change['tag'] == 'insert') {
                    $html .= $fun('diff-insert-full color-bg-diff-insert-bg', $change['changed']['lines']);
                }
                // Show deleted lines only on the left side
                else if($change['tag'] == 'delete') {
                    $html .= $fun('diff-delete-full color-bg-diff-delete-bg', $change['base']['lines']);
                }
                // Show modified lines on both sides
                else if($change['tag'] == 'replace') {
                    $html .= $fun('diff-delete-replace color-bg-diff-delete-bg', $change['base']['lines']);
                    $html .= $fun('diff-insert-replace color-bg-diff-insert-bg', $change['changed']['lines']);
                }
            }
        }
        $html .= '</div>';
        return $html;
    }
}
