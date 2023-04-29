import { FuseOptions } from 'fuse.js';
import { Choices } from './choices';
import { Choice } from './choice';
import { ClassNames } from './class-names';
import { PositionOptionsType } from './position-options-type';
import { Types } from './types';

/**
 * Choices options interface
 *
 * **Terminology**
 *
 * - **Choice:** A choice is a value a user can select. A choice would be equivalent to the `<option></option>` element within a select input.
 * - **Group:** A group is a collection of choices. A group should be seen as equivalent to a `<optgroup></optgroup>` element within a select input.
 * - **Item:** An item is an inputted value **_(text input)_** or a selected choice **_(select element)_**. In the context of a select element, an item is equivelent to a selected option element: `<option value="Hello" selected></option>` whereas in the context of a text input an item is equivelant to `<input type="text" value="Hello">`
 */
export interface Options {
  /**
   * Optionally suppress console errors and warnings.
   *
   * **Input types affected:** text, select-single, select-multiple
   *
   * @Default false
   */
  silent: boolean;

  /**
   * Add pre-selected items (see terminology) to text input.
   *
   * **Input types affected:** text
   *
   * @example
   * ```
   * ['value 1', 'value 2', 'value 3']
   * ```
   *
   * @example
   * ```
   * [{
   *    value: 'Value 1',
   *    label: 'Label 1',
   *    id: 1
   *  },
   *  {
   *    value: 'Value 2',
   *    label: 'Label 2',
   *    id: 2,
   *    customProperties: {
   *      random: 'I am a custom property'
   *  }
   * }]
   * ```
   *
   * @Default []
   */
  items: string[] | Choice[];

  /**
   * Add choices (see terminology) to select input.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @example
   * ```
   * [{
   *   value: 'Option 1',
   *   label: 'Option 1',
   *   selected: true,
   *   disabled: false,
   * },
   * {
   *   value: 'Option 2',
   *   label: 'Option 2',
   *   selected: false,
   *   disabled: true,
   *   customProperties: {
   *     description: 'Custom description about Option 2',
   *     random: 'Another random custom property'
   *   },
   * }]
   * ```
   *
   * @Default []
   */
  choices: Choice[];

  /**
   * The amount of choices to be rendered within the dropdown list `("-1" indicates no limit)`. This is useful if you have a lot of choices where it is easier for a user to use the search area to find a choice.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @Default -1
   */
  renderChoiceLimit: number;

  /**
   * The amount of items a user can input/select `("-1" indicates no limit)`.
   *
   * **Input types affected:** text, select-multiple
   *
   * @Default -1
   */
  maxItemCount: number;

  /**
   * Whether a user can add items.
   *
   * **Input types affected:** text
   *
   * @Default true
   */
  addItems: boolean;

  /**
   * A filter that will need to pass for a user to successfully add an item.
   *
   * **Input types affected:** text
   *
   * @Default null
   */
  addItemFilter: string | RegExp | Types.FilterFunction | null;

  /**
   * The text that is shown when a user has inputted a new item but has not pressed the enter key. To access the current input value, pass a function with a `value` argument (see the **Default config** [https://github.com/jshjohnson/Choices#setup] for an example), otherwise pass a string.
   *
   * **Input types affected:** text
   *
   * @Default
   * ```
   * (value) => `Press Enter to add <b>"${value}"</b>`;
   * ```
   */
  addItemText: string | Types.NoticeStringFunction;

  /**
   * Whether a user can remove items.
   *
   * **Input types affected:** text, select-multiple
   *
   * @Default true
   */
  removeItems: boolean;

  /**
   * Whether each item should have a remove button.
   *
   * **Input types affected:** text, select-one, select-multiple
   *
   * @Default false
   */
  removeItemButton: boolean;

  /**
   * Whether a user can edit items. An item's value can be edited by pressing the backspace.
   *
   * **Input types affected:** text
   *
   * @Default false
   */
  editItems: boolean;

  /**
   * Whether each inputted/chosen item should be unique.
   *
   * **Input types affected:** text, select-multiple
   *
   * @Default true
   */
  duplicateItemsAllowed: boolean;

  /**
   * What divides each value. The Default delimiter separates each value with a comma: `"Value 1, Value 2, Value 3"`.
   *
   * **Input types affected:** text
   *
   * @Default ','
   */
  delimiter: string;

  /**
   * Whether a user can paste into the input.
   *
   * **Input types affected:** text, select-multiple
   *
   * @Default true
   */
  paste: boolean;

  /**
   * Whether a search area should be shown.
   *
   * @note Multiple select boxes will always show search areas.
   *
   * **Input types affected:** select-one
   *
   * @Default true
   */
  searchEnabled: boolean;

  /**
   * Whether choices should be filtered by input or not. If `false`, the search event will still emit, but choices will not be filtered.
   *
   * **Input types affected:** select-one
   *
   * @Default true
   */
  searchChoices: boolean;

  /**
   * The minimum length a search value should be before choices are searched.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @Default 1
   */
  searchFloor: number;

  /**
   * The maximum amount of search results to show.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @Default 4
   */
  searchResultLimit: number;

  /**
   * Specify which fields should be used when a user is searching. If you have added custom properties to your choices, you can add these values thus: `['label', 'value', 'customProperties.example']`.
   *
   * Input types affected:select-one, select-multiple
   *
   * @Default ['label', 'value']
   */
  searchFields: string[];

  /**
   * Whether the dropdown should appear above `(top)` or below `(bottom)` the input. By Default, if there is not enough space within the window the dropdown will appear above the input, otherwise below it.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @Default 'auto'
   */
  position: PositionOptionsType;

  /**
   * Whether the scroll position should reset after adding an item.
   *
   * **Input types affected:** select-multiple
   *
   * @Default true
   */
  resetScrollPosition: boolean;

  /**
   * Whether choices and groups should be sorted. If false, choices/groups will appear in the order they were given.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @Default true
   */
  shouldSort: boolean;

  /**
   * Whether items should be sorted. If false, items will appear in the order they were selected.
   *
   * **Input types affected:** text, select-multiple
   *
   * @Default false
   */
  shouldSortItems: boolean;

  /**
   * The function that will sort choices and items before they are displayed (unless a user is searching). By Default choices and items are sorted by alphabetical order.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @example
   * ```
   * // Sorting via length of label from largest to smallest
   * const example = new Choices(element, {
   *   sorter: function(a, b) {
   *     return b.label.length - a.label.length;
   *   },
   * };
   * ```
   *
   * @Default sortByAlpha
   */
  sorter: (current: Choice, next: Choice) => number;

  /**
   * Whether the input should show a placeholder. Used in conjunction with `placeholderValue`. If `placeholder` is set to true and no value is passed to `placeholderValue`, the passed input's placeholder attribute will be used as the placeholder value.
   *
   * **Input types affected:** text, select-multiple
   *
   * @note For single select boxes, the recommended way of adding a placeholder is as follows:
   * ```
   * <select>
   *   <option placeholder>This is a placeholder</option>
   *   <option>...</option>
   *   <option>...</option>
   *   <option>...</option>
   * </select>
   * ```
   *
   * @Default true
   */
  placeholder: boolean;

  /**
   * The value of the inputs placeholder.
   *
   * **Input types affected:** text, select-multiple
   *
   * @Default null
   */
  placeholderValue: string | null;

  /**
   * The value of the search inputs placeholder.
   *
   * **Input types affected:** select-one
   *
   * @Default null
   */
  searchPlaceholderValue: string | null;

  /**
   * Prepend a value to each item added/selected.
   *
   * **Input types affected:** text, select-one, select-multiple
   *
   * @Default null
   */
  prependValue: string | null;

  /**
   * Append a value to each item added/selected.
   *
   * **Input types affected:** text, select-one, select-multiple
   *
   * @Default null
   */
  appendValue: string | null;

  /**
   * Whether selected choices should be removed from the list. By Default choices are removed when they are selected in multiple select box. To always render choices pass `always`.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @Default 'auto';
   */
  renderSelectedChoices: 'auto' | 'always';

  /**
   * The text that is shown whilst choices are being populated via AJAX.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @Default 'Loading...'
   */
  loadingText: string;

  /**
   * The text that is shown when a user's search has returned no results. Optionally pass a function returning a string.
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @Default 'No results found'
   */
  noResultsText: string | Types.StringFunction;

  /**
   * The text that is shown when a user has selected all possible choices. Optionally pass a function returning a string.
   *
   * **Input types affected:** select-multiple
   *
   * @Default 'No choices to choose from'
   */
  noChoicesText: string | Types.StringFunction;

  /**
   * The text that is shown when a user hovers over a selectable choice.
   *
   * **Input types affected:** select-multiple, select-one
   *
   * @Default 'Press to select'
   */
  itemSelectText: string;

  /**
   * The text that is shown when a user has focus on the input but has already reached the **max item count** [https://github.com/jshjohnson/Choices#maxitemcount]. To access the max item count, pass a function with a `maxItemCount` argument (see the **Default config** [https://github.com/jshjohnson/Choices#setup] for an example), otherwise pass a string.
   *
   * **Input types affected:** text
   *
   * @Default
   * ```
   * (maxItemCount) => `Only ${maxItemCount} values can be added.`;
   * ```
   */
  maxItemText: string | Types.NoticeLimitFunction;

  /**
   * If no duplicates are allowed, and the value already exists in the array.
   *
   * @Default 'Only unique values can be added'
   */
  uniqueItemText: string | Types.NoticeStringFunction;

  /**
   * The text that is shown when addItemFilter is passed and it returns false
   *
   * **Input types affected:** text
   *
   * @Default 'Only values matching specific conditions can be added'
   */
  customAddItemText: string | Types.NoticeStringFunction;

  /**
   * Compare choice and value in appropriate way (e.g. deep equality for objects). To compare choice and value, pass a function with a `valueComparer` argument (see the [Default config](https://github.com/jshjohnson/Choices#setup) for an example).
   *
   * **Input types affected:** select-one, select-multiple
   *
   * @Default
   * ```
   * (choice, item) => choice === item;
   * ```
   */
  valueComparer: Types.ValueCompareFunction;

  /**
   * Classes added to HTML generated by  By Default classnames follow the BEM notation.
   *
   * **Input types affected:** text, select-one, select-multiple
   */
  classNames: ClassNames;

  /**
   * Choices uses the great Fuse library for searching. You can find more options here: https://github.com/krisk/Fuse#options
   */
  fuseOptions: FuseOptions<Choice>;

  /**
   * Function to run once Choices initialises.
   *
   * **Input types affected:** text, select-one, select-multiple
   *
   * @note For each callback, this refers to the current instance of  This can be useful if you need access to methods `(this.disable())` or the config object `(this.config)`.
   *
   * @Default null
   */
  callbackOnInit: ((this: Choices) => void) | null;

  /**
   * Function to run on template creation. Through this callback it is possible to provide custom templates for the various components of Choices (see terminology). For Choices to work with custom templates, it is important you maintain the various data attributes defined here [https://github.com/jshjohnson/Choices/blob/67f29c286aa21d88847adfcd6304dc7d068dc01f/Assets/Scripts/src/choices.js#L1993-L2067].
   *
   * **Input types affected:** text, select-one, select-multiple
   *
   * @note For each callback, this refers to the current instance of  This can be useful if you need access to methods `(this.disable())` or the config object `(this.config)`.
   *
   * @example
   * ```
   * const example = new Choices(element, {
   *   callbackOnCreateTemplates: function (template) {
   *     var classNames = this.config.classNames;
   *     return {
   *       item: (data) => {
   *         return template(`
   *           <div class="${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable}" data-item data-id="${data.id}" data-value="${data.value}" ${data.active ? 'aria-selected="true"' : ''} ${data.disabled ? 'aria-disabled="true"' : ''}>
   *             <span>&bigstar;</span> ${data.label}
   *           </div>
   *         `);
   *       },
   *       choice: (data) => {
   *         return template(`
   *           <div class="${classNames.item} ${classNames.itemChoice} ${data.disabled ? classNames.itemDisabled : classNames.itemSelectable}" data-select-text="${this.config.itemSelectText}" data-choice ${data.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable'} data-id="${data.id}" data-value="${data.value}" ${data.groupId > 0 ? 'role="treeitem"' : 'role="option"'}>
   *             <span>&bigstar;</span> ${data.label}
   *           </div>
   *         `);
   *       },
   *     };
   *   }
   * });
   * ```
   *
   * @Default null
   */
  callbackOnCreateTemplates: ((template: Types.StrToEl) => void) | null;
}
